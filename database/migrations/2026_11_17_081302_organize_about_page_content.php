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

        $pageId = (int) $pageId;

        $this->updateOverview($pageId);
        $this->ensureApproachSection($pageId);

        DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_profile')
            ->where('sort_order', 20)
            ->update(['sort_order' => 30, 'updated_at' => now()]);

        DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_leadership')
            ->where('sort_order', 30)
            ->update(['sort_order' => 40, 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep the organized About content in place when rolling back this data migration.
    }

    private function updateOverview(int $pageId): void
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
        $restoredOverview = '<p>We are an intelligence-led consultancy working across strategic advisory, project and programme management, infrastructure development, governance and decision-support services.</p><p>We bring together technical expertise, strategic thinking, analytical capability and professional advisory services to help decision-makers assess options, understand project considerations and move forward with greater clarity.</p>';
        $legacyGeographicFocus = '<p>BEAL’s strategic ambition is to serve clients across East Africa and the wider African market.</p>';
        $restoredGeographicFocus = '<p>Based in Nairobi, Kenya, we aim to serve clients across East Africa and the wider African market. As our business grows, our platform is intended to expand with research publications, analytical products, digital solutions and additional advisory services.</p>';
        $updated = false;

        if (blank(strip_tags((string) ($content['overview'] ?? ''))) || in_array($content['overview'] ?? null, [$legacyOverview, $restoredOverview], true)) {
            $content['overview'] = '<p>Founded in Kenya on 16 July 2021, we are an Africa-focused built-environment and project advisory consultancy. We provide integrated project and programme management, construction management, development monitoring, project controls and technical coordination.</p><p>We work with developers, investors, institutions and organisations to strengthen project planning, delivery oversight, governance and performance. By combining practical construction delivery experience with structured project management, multidisciplinary coordination and performance-based reporting, we help clients make informed decisions and achieve successful project outcomes.</p>';
            $updated = true;
        }

        if (blank(strip_tags((string) ($content['geographic_focus'] ?? ''))) || in_array($content['geographic_focus'] ?? null, [$legacyGeographicFocus, $restoredGeographicFocus], true)) {
            $content['geographic_focus'] = '<p>Through a flexible network of experienced professionals and specialist consultants, we support projects across Kenya, East Africa and the wider African market.</p>';
            $updated = true;
        }

        if ($updated) {
            DB::table('page_sections')->where('id', $section->id)->update([
                'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    private function ensureApproachSection(int $pageId): void
    {
        $section = DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_approach')
            ->first(['id', 'content']);

        $defaults = [
            'heading' => 'Structure for every project stage.',
            'introduction' => '<p>Successful projects need more than effective construction supervision. They require strategic planning, disciplined coordination, proactive risk management and continuous performance monitoring.</p><p>We bring structure, accountability and visibility to the full project lifecycle, from early planning and development through procurement, implementation, testing, commissioning and handover.</p>',
            'items' => [
                ['title' => 'Project governance', 'description' => 'Establish clear management structures, accountability frameworks and decision-making processes.'],
                ['title' => 'Programme & project controls', 'description' => 'Develop baseline programmes, monitor progress, manage critical activities and identify potential delays.'],
                ['title' => 'Risk & performance management', 'description' => 'Identify project risks, track performance and develop practical mitigation strategies.'],
                ['title' => 'Contract & commercial coordination', 'description' => 'Support contract administration, procurement management, claims assessment and contractual compliance.'],
                ['title' => 'Technical & stakeholder coordination', 'description' => 'Facilitate collaboration between clients, consultants, contractors, suppliers and other project stakeholders.'],
                ['title' => 'Quality & delivery assurance', 'description' => 'Monitor project quality, construction standards, completion requirements and handover processes.'],
            ],
            'objective' => '<p>Our objective is to give clients greater control, confidence and visibility over their projects.</p>',
        ];

        if ($section === null) {
            $timestamp = now();

            DB::table('page_sections')->insert([
                'website_page_id' => $pageId,
                'section_key' => 'about_approach',
                'label' => 'Our approach',
                'content' => json_encode($defaults, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'media_asset_id' => null,
                'is_enabled' => true,
                'sort_order' => 20,
                'updated_by' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            return;
        }

        $content = json_decode((string) $section->content, true);

        if (! is_array($content) || (blank(strip_tags((string) ($content['introduction'] ?? ''))) && empty($content['items']))) {
            DB::table('page_sections')->where('id', $section->id)->update([
                'content' => json_encode($defaults, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }
};
