<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PROFILE_SLUG = 'douglas-murithi-kathuri';

    private const BIO = [
        'Douglas Murithi Kathuri is a Partner & Director and an experienced Environment, Health, Safety and Sustainability (EHSS) professional with more than nine years of multidisciplinary experience across infrastructure, engineering and construction, energy, oil and gas, industrial operations, and Testing, Inspection and Certification assignments in Africa. He combines regional EHSS leadership with environmental and social safeguards, construction safety management, environmental consultancy, industrial hygiene, technical inspection and monitoring, and regulatory compliance.',
        'His technical expertise includes environmental and social impact assessment, environmental permitting and compliance, construction HSE and contractor management, risk assessment, environmental monitoring, industrial hygiene, emissions testing, occupational health and safety, and ESG and sustainability performance.',
        'Douglas has supported road infrastructure, buildings and civil works, industrial facilities, power and energy installations, oil and gas operations, and other regulated developments. His assignments have involved organisations including Bureau Veritas, SGS, TotalEnergies, Eni Natural Energies, Oryx Energies, Contour Global KivuWatt, KeNHA and China Wu Yi. His regional experience covers Kenya, Uganda, Ethiopia, Somalia, Rwanda and Angola, including road infrastructure work in Puntland, Somalia, and environmental and industrial hygiene assignments in the energy sector in Angola and Rwanda.',
        'Across project development, construction, commissioning and operations, Douglas works to integrate environmental protection, worker health and safety, social safeguards and sustainability. He brings technical depth, project execution, regional management, stakeholder engagement and strategic consultancy to support safe, compliant and sustainable delivery across Africa.',
    ];

    private const PREVIOUS_BIO = 'Douglas Murithi Kathuri is a Partner & Director and an experienced Environment, Health, Safety and Sustainability (EHSS) professional with more than nine years of multidisciplinary experience supporting infrastructure, engineering and construction, energy, oil and gas, industrial operations, and Testing, Inspection and Certification assignments across Africa. His work spans regional EHSS leadership, environmental and social safeguards, construction safety management, environmental consultancy, industrial hygiene, technical inspection and monitoring, and regulatory compliance. He has supported road infrastructure, buildings and civil works, industrial facilities, power and energy installations, oil and gas operations and other regulated developments, working on assignments involving Bureau Veritas, SGS, TotalEnergies, Eni Natural Energies, Oryx Energies, Contour Global KivuWatt, KeNHA and China Wu Yi. His regional experience includes Kenya, Uganda, Ethiopia, Somalia, Rwanda and Angola, including road infrastructure work in Puntland, Somalia, and environmental and industrial hygiene assignments in the energy sector in Angola and Rwanda. Douglas’s areas of expertise include environmental and social impact assessment, environmental permitting and compliance, construction HSE and contractor management, risk assessment, environmental monitoring, industrial hygiene, emissions testing, occupational health and safety, ESG and sustainability performance. He works to integrate environmental protection, worker health and safety, social safeguards and sustainability through project development, construction, commissioning and operations, bringing technical depth, project execution, regional management, stakeholder engagement and strategic consultancy to support safe, compliant and sustainable delivery across Africa.';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->updateProfileBio(self::BIO);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->updateProfileBio([self::PREVIOUS_BIO]);
    }

    /**
     * @param  array<int, string>  $bio
     */
    private function updateProfileBio(array $bio): void
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

            if (! is_array($content) || ! is_array($content['profiles'] ?? null)) {
                continue;
            }

            foreach ($content['profiles'] as &$profile) {
                if (is_array($profile) && ($profile['slug'] ?? null) === self::PROFILE_SLUG) {
                    $profile['bio'] = $bio;
                }
            }
            unset($profile);

            $updates[$field] = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }
};
