<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PROFILE_SLUG = 'justin-munene-kiriinya';

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

    private function updateTeamProfiles(bool $addProfile): void
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

        $profile = [
            'name' => 'Justin Munene Kiriinya',
            'slug' => self::PROFILE_SLUG,
            'designation' => 'Head of Energy & Utilities',
            'role' => 'Strategic Technical Associate',
            'image' => '/assets/team/justin-munene-kiriinya.jpeg',
            'image_alt' => 'Portrait of Justin Munene Kiriinya, Head of Energy and Utilities',
            'introduction' => '',
            'bio' => [
                'Justin Munene Kiriinya is an Electrical and Electronic Engineer specialising in electrical infrastructure, energy systems, building services engineering and renewable energy solutions. He brings over 10 years of professional experience across engineering consultancy, technical design, project supervision, contract administration and commissioning of electrical installations.',
                'He holds a Master of Science in Sustainable Energy Transitions from Strathmore University and a Bachelor of Science in Electrical and Electronic Engineering from Jomo Kenyatta University of Agriculture and Technology (JKUAT). He is a Registered Professional Engineer with the Engineers Board of Kenya (EBK), a Corporate Member of the Institution of Engineers of Kenya (IEK), and holds an EPRA A-1 Electrician Licence. His specialist training includes solar energy systems through the Strathmore Energy Research Centre.',
                'His technical expertise includes electrical distribution systems, power generation and backup solutions, off-grid, grid-tied and hybrid solar photovoltaic installations, power conditioning, UPS installations, fire detection and alarm systems, access control, security systems and structured cabling. His experience also covers electrical engineering design, bills of quantities, technical specifications, site supervision, contract management, financial appraisals, testing and commissioning.',
                'Across earlier professional engagements, Justin has contributed to commercial, institutional, hospitality, healthcare, industrial and residential developments. His project experience includes the Standard Chartered Bank Data Centre Upgrade, KURA Traffic Management Centre, Kitale County Teaching and Referral Hospital, Scania Service Centre, Kenya Wine Agency Limited Factory, Kenya Forces Research and Referral Hospital, IFC and UNHCR office developments, and Food4Education industrial cooking facilities across multiple locations.',
                'Alongside his technical work, Justin has experience in business development, professional collaboration and coordinating multidisciplinary consultants, including international engineering teams.',
                'At BEAL Consulting Ltd, he provides strategic technical leadership for the Energy & Utilities practice, bringing specialist engineering expertise, energy infrastructure advisory and sustainable energy solutions to the firm’s multidisciplinary project and infrastructure teams.',
            ],
            'specialisms' => [],
            'sections' => [],
            'seo_title' => 'Justin Munene Kiriinya | Energy & Utilities | BEAL Consulting',
            'seo_description' => 'Justin Munene Kiriinya leads BEAL Consulting’s Energy & Utilities practice, bringing 10+ years in electrical engineering, energy systems and renewable solutions.',
        ];

        $updates = [];

        foreach (['content', 'draft_content'] as $field) {
            $content = json_decode((string) $section->{$field}, true);

            if (! is_array($content)) {
                continue;
            }

            $profiles = array_values(array_filter($content['profiles'] ?? [], 'is_array'));
            $existingProfileIndex = collect($profiles)->search(fn (array $existingProfile): bool => ($existingProfile['slug'] ?? null) === self::PROFILE_SLUG);

            if ($addProfile && $existingProfileIndex === false) {
                $profiles[] = $profile;
            } elseif (! $addProfile && $existingProfileIndex !== false) {
                unset($profiles[$existingProfileIndex]);
            }

            $content['profiles'] = array_values($profiles);
            $updates[$field] = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }
};
