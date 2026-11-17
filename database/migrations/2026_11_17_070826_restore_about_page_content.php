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
        $pageId = DB::table('website_pages')->where('page_key', 'about')->value('id');

        if ($pageId === null) {
            return;
        }

        $this->restoreOverview((int) $pageId);
        $this->restoreProfile((int) $pageId);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep restored CMS content when rolling back this data migration.
    }

    private function restoreOverview(int $pageId): void
    {
        $section = DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_overview')
            ->first(['id', 'content']);

        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);

        if (! is_array($content)) {
            return;
        }

        $legacyOverview = '<p>BEAL Consulting Ltd is an intelligence-led consultancy operating across strategic advisory, project and programme management, infrastructure development, governance and decision-support services.</p>';
        $legacyGeographicFocus = '<p>BEAL’s strategic ambition is to serve clients across East Africa and the wider African market.</p>';
        $updated = false;

        if (blank(strip_tags((string) ($content['overview'] ?? ''))) || ($content['overview'] ?? null) === $legacyOverview) {
            $content['overview'] = '<p>We are an intelligence-led consultancy working across strategic advisory, project and programme management, infrastructure development, governance and decision-support services.</p><p>We bring together technical expertise, strategic thinking, analytical capability and professional advisory services to help decision-makers assess options, understand project considerations and move forward with greater clarity.</p>';
            $updated = true;
        }

        if (blank(strip_tags((string) ($content['geographic_focus'] ?? ''))) || ($content['geographic_focus'] ?? null) === $legacyGeographicFocus) {
            $content['geographic_focus'] = '<p>Based in Nairobi, Kenya, we aim to serve clients across East Africa and the wider African market. As our business grows, our platform is intended to expand with research publications, analytical products, digital solutions and additional advisory services.</p>';
            $updated = true;
        }

        if ($updated) {
            DB::table('page_sections')->where('id', $section->id)->update([
                'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    private function restoreProfile(int $pageId): void
    {
        $section = DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_profile')
            ->first(['id', 'content']);

        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);

        if (! is_array($content)) {
            return;
        }

        $defaults = [
            'mission' => '<p>To bring intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions, helping clients evaluate choices and move forward with clarity.</p>',
            'vision' => '<p>To build a trusted African consultancy that supports better project outcomes through analytical capability, technical competence and forward-looking advisory.</p>',
            'philosophy' => '<p>We look beyond the immediate task. By understanding context, connecting information and considering delivery implications, we help clients make informed decisions with confidence.</p>',
            'values' => [
                'Intelligence and analytical thinking',
                'Professionalism and institutional credibility',
                'Strategic insight and sound decision-making',
                'Technical competence and project delivery expertise',
                'Innovation and forward-looking solutions',
            ],
        ];
        $updated = false;

        foreach (['mission', 'vision', 'philosophy'] as $field) {
            if (blank(strip_tags((string) ($content[$field] ?? '')))) {
                $content[$field] = $defaults[$field];
                $updated = true;
            }
        }

        if (! is_array($content['values'] ?? null)) {
            $decodedValues = json_decode((string) ($content['values_json'] ?? '[]'), true);
            $content['values'] = is_array($decodedValues) ? $decodedValues : [];
            $updated = true;
        }

        if ($content['values'] === []) {
            $content['values'] = $defaults['values'];
            $updated = true;
        }

        if (array_key_exists('values_json', $content)) {
            unset($content['values_json']);
            $updated = true;
        }

        if ($updated) {
            DB::table('page_sections')->where('id', $section->id)->update([
                'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }
};
