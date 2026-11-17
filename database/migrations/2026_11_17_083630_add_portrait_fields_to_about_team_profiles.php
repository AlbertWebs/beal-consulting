<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('website_pages')->where('page_key', 'about')->value('id');

        if ($pageId === null) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_leadership')
            ->first(['id', 'content']);

        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);

        if (! is_array($content) || ! is_array($content['profiles'] ?? null)) {
            return;
        }

        $updated = false;

        foreach ($content['profiles'] as &$profile) {
            if (! is_array($profile)) {
                continue;
            }

            if (! array_key_exists('image', $profile)) {
                $profile['image'] = '';
                $updated = true;
            }

            if (! array_key_exists('image_alt', $profile)) {
                $profile['image_alt'] = 'Portrait of '.trim((string) ($profile['name'] ?? 'team member'));
                $updated = true;
            }
        }
        unset($profile);

        if ($updated) {
            DB::table('page_sections')->where('id', $section->id)->update([
                'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Preserve portrait fields and team content if this data migration is rolled back.
    }
};
