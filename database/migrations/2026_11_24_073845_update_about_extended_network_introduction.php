<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PREVIOUS_COPY = '<p>Meet the wider team contributing across business development, structural engineering and quantity surveying.</p>';

    private const UPDATED_COPY = '<p>Our extended network of specialists brings together strategic, technical and commercial expertise to support complex projects, infrastructure development and organisational performance.</p>';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->replaceIntroduction(self::PREVIOUS_COPY, self::UPDATED_COPY);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceIntroduction(self::UPDATED_COPY, self::PREVIOUS_COPY);
    }

    private function replaceIntroduction(string $from, string $to): void
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

            if (! is_array($content) || ($content['network_introduction'] ?? null) !== $from) {
                continue;
            }

            $content['network_introduction'] = $to;
            $updates[$field] = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }
};
