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

        $this->populateSection((int) $pageId, 'about_leadership', 'Leadership & professional profile', 40, [
            'introduction' => '<p>Meet the people who bring practical delivery experience, technical understanding and project leadership to BEAL Consulting.</p>',
            'profiles' => [[
                'name' => 'Benson Mathia Ng’ang’a',
                'role' => 'Founder & Director | PMP® | Construction & Project Management Professional',
                'bio' => [
                    'Benson Mathia Ng’ang’a is the Founder and Director of BEAL Consulting Ltd, bringing over a decade of professional experience in construction and project management across commercial, residential, hospitality, industrial and infrastructure developments.',
                    'He is a Project Management Professional (PMP®) with extensive contractor-side project delivery experience, covering the full project lifecycle from planning and mobilisation through construction, multidisciplinary coordination, testing, commissioning and handover.',
                    'His expertise includes project planning, programme and cost management, contract administration, procurement coordination, risk management, quality assurance, subcontractor management and stakeholder engagement.',
                    'Benson holds a Master of Science in Construction Engineering Management from the University of East London and a Bachelor of Science in Construction Management from the University of Nairobi. He combines technical construction knowledge with practical project leadership and a strong appreciation of governance, commercial performance and strategic project delivery.',
                    'Through BEAL, he brings together experienced professionals and specialist expertise to support well-planned, efficiently managed and successfully delivered projects across Africa.',
                ],
            ]],
        ]);

        $this->populateSection((int) $pageId, 'about_experience', 'Professional experience & project exposure', 50, [
            'experience_introduction' => '<p>BEAL’s leadership draws on experience gained through the planning, coordination and delivery of projects across diverse sectors of the built environment.</p>',
            'experience_areas' => [
                'Project inception, planning and mobilisation',
                'Construction and programme management',
                'Baseline programme development and progress monitoring',
                'Project controls, reporting and performance assessment',
                'Contract administration and claims coordination',
                'Procurement and subcontractor management',
                'Cost, quality, risk and resource management',
                'Multidisciplinary technical and stakeholder coordination',
                'Testing, commissioning and project handover',
            ],
            'projects' => [
                'Itare Fresh Water Dam Project',
                'Onyx Apartments',
                'East African Breweries Limited (EABL) Headquarters Fit-out',
                'Scania East Africa Facilities',
                'EABL Microbrewery Project',
                'Africa Area Office Development',
                'British American Tobacco (BAT) Headquarters Refurbishment',
            ],
            'attribution_note' => '<p>These projects reflect the Founding Director’s professional experience gained through previous engagements. They are not presented as projects directly contracted by BEAL Consulting Ltd.</p>',
        ]);
    }

    public function down(): void
    {
        // Preserve the published team and experience content if this data migration is rolled back.
    }

    private function populateSection(int $pageId, string $key, string $label, int $order, array $defaults): void
    {
        $section = DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', $key)
            ->first(['id', 'content']);

        $timestamp = now();

        if ($section === null) {
            DB::table('page_sections')->insert([
                'website_page_id' => $pageId,
                'section_key' => $key,
                'label' => $label,
                'content' => json_encode($defaults, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'media_asset_id' => null,
                'is_enabled' => true,
                'sort_order' => $order,
                'updated_by' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            return;
        }

        $content = json_decode((string) $section->content, true);
        $content = is_array($content) ? $content : [];

        foreach ($defaults as $field => $value) {
            $empty = is_array($value)
                ? empty($content[$field])
                : blank(strip_tags((string) ($content[$field] ?? '')));

            if (! array_key_exists($field, $content) || $empty) {
                $content[$field] = $value;
            }
        }

        DB::table('page_sections')->where('id', $section->id)->update([
            'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => $timestamp,
        ]);
    }
};
