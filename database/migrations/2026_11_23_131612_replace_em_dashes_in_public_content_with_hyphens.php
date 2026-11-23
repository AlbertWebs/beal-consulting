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
        $emDash = "\u{2014}";

        $publicTextColumns = [
            'page_sections' => ['label', 'content', 'draft_content'],
            'site_settings' => ['value'],
            'website_pages' => ['title', 'navigation_label', 'seo_title', 'seo_description'],
            'website_services' => ['number', 'title', 'description', 'description_html', 'subservices', 'content', 'draft_content', 'seo_title', 'seo_description'],
            'content_entries' => ['type', 'title', 'summary', 'content_html', 'category', 'author', 'location', 'scale', 'scope', 'outcomes', 'experience_source', 'image_alt', 'seo_title', 'seo_description', 'draft_data', 'gallery_stages'],
        ];

        foreach ($publicTextColumns as $table => $columns) {
            foreach ($columns as $column) {
                DB::table($table)
                    ->where($column, 'like', '%'.$emDash.'%')
                    ->update([$column => DB::raw("REPLACE({$column}, '{$emDash}', '-')")]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Preserve the cleaned public copy because its original punctuation varied by context.
    }
};
