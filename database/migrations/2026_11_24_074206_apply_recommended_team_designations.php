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
        $this->updateTeamProfiles(true);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->updateTeamProfiles(false);
    }

    private function updateTeamProfiles(bool $useRecommendedDesignations): void
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

        $profiles = [
            'benson-mathia-nganga' => [
                'before' => [
                    'designation' => null,
                    'role' => 'Founder & Director | PMP® | Construction & Project Management Professional',
                    'seo_title' => null,
                    'seo_description' => null,
                ],
                'after' => [
                    'designation' => 'Founder & Managing Director',
                    'role' => 'PMP® | Construction & Project Management Professional',
                    'seo_title' => 'Benson Mathia Ng’ang’a | Founder & Managing Director | BEAL Consulting',
                    'seo_description' => 'Benson Mathia Ng’ang’a is the Founder & Managing Director of BEAL Consulting, with more than 10 years of construction and infrastructure project management experience.',
                ],
            ],
            'douglas-murithi-kathuri' => [
                'before' => [
                    'designation' => 'Partner & Director',
                    'role' => 'Regional EHSS & Sustainability Professional',
                    'seo_title' => 'Douglas Murithi Kathuri | Partner & Director, EHSS | BEAL Consulting',
                    'seo_description' => 'Douglas Murithi Kathuri is a Partner & Director at BEAL Consulting, bringing 9+ years of EHSS and sustainability experience across infrastructure, construction, energy and industry in Africa.',
                ],
                'after' => [
                    'designation' => 'Lead Consultant – EHSS & Sustainability',
                    'role' => 'Lead Consultant – EHSS & Sustainability',
                    'seo_title' => 'Douglas Murithi Kathuri | Lead Consultant, EHSS & Sustainability | BEAL Consulting',
                    'seo_description' => 'Douglas Murithi Kathuri is BEAL Consulting’s Lead Consultant for EHSS and Sustainability, with more than nine years of regional experience supporting complex infrastructure, construction, energy and industrial assignments across Africa.',
                ],
            ],
            'lydiah-kanyiri' => [
                'before' => [
                    'designation' => 'Partner & Director',
                    'role' => 'Construction Project Manager',
                    'seo_title' => 'Lydiah Kanyiri | Partner & Director | BEAL Consulting',
                    'seo_description' => 'Lydiah Kanyiri is a Partner & Director at BEAL Consulting and a construction project manager with 10+ years delivering complex and distressed developments across Kenya.',
                ],
                'after' => [
                    'designation' => 'Construction Project Management Lead',
                    'role' => 'Construction Project Management Lead',
                    'seo_title' => 'Lydiah Kanyiri | Construction Project Management Lead | BEAL Consulting',
                    'seo_description' => 'Lydiah Kanyiri is BEAL Consulting’s Construction Project Management Lead, with over 10 years delivering complex, stalled and high-pressure developments across Kenya.',
                ],
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

                $slug = (string) ($profile['slug'] ?? '');
                $values = $profiles[$slug][$useRecommendedDesignations ? 'after' : 'before'] ?? null;

                if (! is_array($values)) {
                    continue;
                }

                foreach ($values as $key => $value) {
                    if ($value === null) {
                        unset($profile[$key]);
                    } else {
                        $profile[$key] = $value;
                    }
                }

                $changed = true;
            }
            unset($profile);

            if ($changed) {
                $content['profiles'] = array_values($content['profiles']);
                $updates[$field] = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }
};
