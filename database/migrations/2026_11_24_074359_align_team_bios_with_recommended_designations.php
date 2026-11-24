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
        $this->replaceBioText(true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceBioText(false);
    }

    private function replaceBioText(bool $useRecommendedCopy): void
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

        $replacements = [
            'douglas-murithi-kathuri' => [
                'before' => 'Douglas Murithi Kathuri is a Partner & Director and an experienced',
                'after' => 'Douglas Murithi Kathuri is BEAL Consulting’s Lead Consultant for EHSS and Sustainability and an experienced',
            ],
            'lydiah-kanyiri' => [
                'before' => 'Lydiah Kanyiri is a Partner & Director and construction project manager with more than 10 years’ experience',
                'after' => 'Lydiah Kanyiri is BEAL Consulting’s Construction Project Management Lead, with more than 10 years’ experience',
            ],
        ];

        $updates = [];

        foreach (['content', 'draft_content'] as $field) {
            $content = json_decode((string) $section->{$field}, true);

            if (! is_array($content) || ! is_array($content['profiles'] ?? null)) {
                continue;
            }

            $changed = false;

            foreach ($content['profiles'] as &$profile) {
                if (! is_array($profile)) {
                    continue;
                }

                $replacement = $replacements[(string) ($profile['slug'] ?? '')] ?? null;

                if (! is_array($replacement) || ! is_array($profile['bio'] ?? null)) {
                    continue;
                }

                $from = $replacement[$useRecommendedCopy ? 'before' : 'after'];
                $to = $replacement[$useRecommendedCopy ? 'after' : 'before'];

                foreach ($profile['bio'] as &$paragraph) {
                    if (! is_string($paragraph) || ! str_contains($paragraph, $from)) {
                        continue;
                    }

                    $paragraph = str_replace($from, $to, $paragraph);
                    $changed = true;
                }
                unset($paragraph);
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
