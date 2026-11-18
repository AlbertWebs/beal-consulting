<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $aboutPageId = DB::table('website_pages')->where('page_key', 'about')->value('id');
        if ($aboutPageId === null) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('website_page_id', $aboutPageId)
            ->where('section_key', 'about_leadership')
            ->first(['id', 'content']);
        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);
        if (! is_array($content) || ! is_array($content['profiles'] ?? null)) {
            return;
        }

        foreach ($content['profiles'] as &$profile) {
            if (is_array($profile) && blank($profile['slug'] ?? null) && filled($profile['name'] ?? null)) {
                $profile['slug'] = Str::slug($profile['name']);
            }
        }
        unset($profile);

        DB::table('page_sections')->where('id', $section->id)->update([
            'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Profile slugs remain useful public URLs after rollback.
    }
};
