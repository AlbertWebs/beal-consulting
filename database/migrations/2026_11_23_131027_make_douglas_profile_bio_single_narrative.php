<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PROFILE_SLUG = 'douglas-murithi-kathuri';

    private const BIO = 'Douglas Murithi Kathuri is a Partner & Director and an experienced Environment, Health, Safety and Sustainability (EHSS) professional with more than nine years of multidisciplinary experience supporting infrastructure, engineering and construction, energy, oil and gas, industrial operations, and Testing, Inspection and Certification assignments across Africa. His work spans regional EHSS leadership, environmental and social safeguards, construction safety management, environmental consultancy, industrial hygiene, technical inspection and monitoring, and regulatory compliance. He has supported road infrastructure, buildings and civil works, industrial facilities, power and energy installations, oil and gas operations and other regulated developments, working on assignments involving Bureau Veritas, SGS, TotalEnergies, Eni Natural Energies, Oryx Energies, Contour Global KivuWatt, KeNHA and China Wu Yi. His regional experience includes Kenya, Uganda, Ethiopia, Somalia, Rwanda and Angola, including road infrastructure work in Puntland, Somalia, and environmental and industrial hygiene assignments in the energy sector in Angola and Rwanda. Douglas’s areas of expertise include environmental and social impact assessment, environmental permitting and compliance, construction HSE and contractor management, risk assessment, environmental monitoring, industrial hygiene, emissions testing, occupational health and safety, ESG and sustainability performance. He works to integrate environmental protection, worker health and safety, social safeguards and sustainability through project development, construction, commissioning and operations, bringing technical depth, project execution, regional management, stakeholder engagement and strategic consultancy to support safe, compliant and sustainable delivery across Africa.';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->updateProfile(function (array $profile): array {
            return [
                ...$profile,
                'introduction' => '',
                'bio' => [self::BIO],
                'specialisms' => [],
                'sections' => [],
            ];
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->updateProfile(function (array $profile): array {
            return [
                ...$profile,
                'introduction' => 'A regional environment, health, safety and sustainability professional supporting complex projects and operations across Africa.',
                'bio' => [
                    'Douglas Murithi Kathuri is an Environment, Health, Safety and Sustainability professional with more than nine years of multidisciplinary experience across infrastructure, engineering and construction, energy, oil and gas, industrial operations, and testing, inspection and certification (TIC) assignments in Africa.',
                    'His background combines regional EHSS leadership with environmental and social safeguards, construction safety management, environmental consultancy, industrial hygiene, technical inspection and monitoring, and regulatory compliance. He helps teams integrate environmental protection, worker health and safety, social safeguards and sustainability from development and construction through commissioning and operations.',
                ],
                'specialisms' => [
                    'Environmental and social impact assessment and safeguards',
                    'Environmental permitting, monitoring and compliance',
                    'Construction HSE and contractor management',
                    'Risk assessment and technical inspection',
                    'Industrial hygiene, emissions testing and occupational health',
                    'ESG and sustainability performance',
                ],
                'sections' => [[
                    'heading' => 'Project and regional experience',
                    'paragraphs' => [
                        'Douglas has supported assignments involving road infrastructure, buildings and civil works, industrial facilities, power and energy installations, oil and gas operations, and other regulated development activities. His work has involved leading industry organisations including Bureau Veritas, SGS, TotalEnergies, Eni Natural Energies, Oryx Energies, Contour Global KivuWatt, KeNHA and China Wu Yi.',
                        'His geographical experience covers Kenya, Uganda, Ethiopia, Somalia, Rwanda and Angola. This includes environmental and social consultancy for road infrastructure projects in Puntland, Somalia, and environmental and industrial hygiene assignments in the energy sector in Angola and Rwanda.',
                    ],
                ], [
                    'heading' => 'Supporting safe and sustainable delivery',
                    'body' => 'Douglas brings together technical delivery, regional management, stakeholder engagement and strategic consultancy. He works to integrate environmental protection, worker health and safety, social safeguards and sustainability throughout project development, construction, commissioning and operations, supporting contractors, developers, engineering consultants, TIC organisations and energy operators across Africa.',
                ]],
            ];
        });
    }

    /**
     * @param  callable(array<string, mixed>): array<string, mixed>  $transform
     */
    private function updateProfile(callable $transform): void
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

            $changed = false;

            foreach ($content['profiles'] as &$profile) {
                if (! is_array($profile) || ($profile['slug'] ?? null) !== self::PROFILE_SLUG) {
                    continue;
                }

                $profile = $transform($profile);
                $changed = true;
            }
            unset($profile);

            if ($changed) {
                $updates[$field] = json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }
};
