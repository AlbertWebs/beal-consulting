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

        $bio = [
            'Benson Mathia Ng’ang’a is a PMP®-certified construction and infrastructure project management professional with over 10 years of experience in the planning, coordination and delivery of complex building, infrastructure and development projects.',
            'He holds a Bachelor of Science degree in Construction Management from the University of Nairobi and a Master’s degree in Construction Engineering Management from the University of London. His professional credentials also include Certified Construction Manager, complemented by extensive project management experience.',
            'Throughout his career, Benson has contributed to the delivery of major commercial, institutional, hospitality, mixed-use and infrastructure developments. His expertise spans project planning, programme controls, contract administration, construction management, risk management, procurement coordination, quality assurance and project governance.',
            'His experience covers the entire project lifecycle, from inception and pre-construction planning through execution, monitoring, testing, commissioning and handover. He has collaborated with developers, consultants, contractors, specialist subcontractors and other stakeholders to strengthen accountability, manage delivery risks and achieve successful project outcomes.',
            'As Founder and Managing Director of BEAL Consulting Ltd, Benson provides the strategic leadership and professional direction behind the firm’s ambition to deliver integrated advisory, project management, infrastructure and development consultancy services.',
            'His vision is to build a respected African consultancy that combines technical expertise, independent thinking, analytical intelligence and disciplined project governance to support better investment decisions and deliver sustainable development outcomes.',
        ];

        $updates = ['updated_at' => now()];
        $content = json_decode((string) $section->content, true);

        if (is_array($content)) {
            $updates['content'] = json_encode($this->updateProfileBio($content, $bio), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (filled($section->draft_content)) {
            $draftContent = json_decode((string) $section->draft_content, true);

            if (is_array($draftContent)) {
                $updates['draft_content'] = json_encode($this->updateProfileBio($draftContent, $bio), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        if (count($updates) > 1) {
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Preserve the updated published biography when rolling back.
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<int, string>  $bio
     * @return array<string, mixed>
     */
    private function updateProfileBio(array $content, array $bio): array
    {
        $profiles = array_values(array_filter($content['profiles'] ?? [], 'is_array'));

        foreach ($profiles as &$profile) {
            if (($profile['slug'] ?? null) === 'benson-mathia-nganga' || ($profile['name'] ?? null) === 'Benson Mathia Ng’ang’a') {
                $profile['bio'] = $bio;
            }
        }

        unset($profile);

        $content['profiles'] = $profiles;

        return $content;
    }
};
