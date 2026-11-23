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

    private function updateTeamProfiles(bool $designate): void
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
            'douglas-murithi-kathuri' => [
                'seo_title' => [
                    'before' => 'Douglas Murithi Kathuri | EHSS & Sustainability | BEAL Consulting',
                    'after' => 'Douglas Murithi Kathuri | Partner & Director, EHSS | BEAL Consulting',
                ],
                'seo_description' => [
                    'before' => 'Douglas Murithi Kathuri is a regional EHSS and sustainability professional with 9+ years across infrastructure, construction, energy, oil and gas, and industry in Africa.',
                    'after' => 'Douglas Murithi Kathuri is a Partner & Director at BEAL Consulting, bringing 9+ years of EHSS and sustainability experience across infrastructure, construction, energy and industry in Africa.',
                ],
            ],
            'lydiah-kanyiri' => [
                'seo_title' => [
                    'before' => 'Lydiah Kanyiri | Construction Project Manager | BEAL Consulting',
                    'after' => 'Lydiah Kanyiri | Partner & Director | BEAL Consulting',
                ],
                'seo_description' => [
                    'before' => 'Meet Lydiah Kanyiri, a construction project manager with 10+ years delivering complex and distressed developments across Kenya.',
                    'after' => 'Lydiah Kanyiri is a Partner & Director at BEAL Consulting and a construction project manager with 10+ years delivering complex and distressed developments across Kenya.',
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

                if (! isset($profiles[$slug])) {
                    continue;
                }

                if ($designate) {
                    $profile['designation'] = 'Partner & Director';
                } elseif (($profile['designation'] ?? null) === 'Partner & Director') {
                    unset($profile['designation']);
                }

                foreach (['seo_title', 'seo_description'] as $seoField) {
                    $from = $designate ? 'before' : 'after';
                    $to = $designate ? 'after' : 'before';

                    if (($profile[$seoField] ?? null) === $profiles[$slug][$seoField][$from]) {
                        $profile[$seoField] = $profiles[$slug][$seoField][$to];
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
