<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->replaceUniversityName('University of London', 'University of East London');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceUniversityName('University of East London', 'University of London');
    }

    private function replaceUniversityName(string $from, string $to): void
    {
        $aboutPageId = DB::table('website_pages')->where('page_key', 'about')->value('id');

        if ($aboutPageId === null) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('website_page_id', $aboutPageId)
            ->where('section_key', 'about_leadership')
            ->first(['id', 'content', 'draft_content']);

        if ($section === null) {
            return;
        }

        $updates = [];

        foreach (['content', 'draft_content'] as $field) {
            $content = json_decode((string) $section->{$field}, true);

            if (! is_array($content) || ! is_array($content['profiles'] ?? null)) {
                continue;
            }

            $changed = false;

            foreach ($content['profiles'] as &$profile) {
                if (! is_array($profile) || ($profile['slug'] ?? null) !== 'benson-mathia-nganga') {
                    continue;
                }

                $bio = is_array($profile['bio'] ?? null) ? $profile['bio'] : [];

                foreach ($bio as &$paragraph) {
                    if (! is_string($paragraph) || ! str_contains($paragraph, $from)) {
                        continue;
                    }

                    $paragraph = str_replace($from, $to, $paragraph);
                    $changed = true;
                }
                unset($paragraph);

                if ($changed) {
                    $profile['bio'] = $bio;
                }
            }
            unset($profile);

            if ($changed) {
                $updates[$field] = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }
};
