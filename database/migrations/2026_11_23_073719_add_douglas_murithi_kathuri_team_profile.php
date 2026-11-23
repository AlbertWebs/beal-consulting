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

        $profile = [
            'name' => 'Douglas Murithi Kathuri',
            'slug' => 'douglas-murithi-kathuri',
            'role' => 'Regional EHSS & Sustainability Professional',
            'image' => '/assets/team/douglas-murithi-kathuri.jpeg',
            'image_alt' => 'Portrait of Douglas Murithi Kathuri, Regional EHSS and Sustainability Professional',
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
            'seo_title' => 'Douglas Murithi Kathuri | EHSS & Sustainability | BEAL Consulting',
            'seo_description' => 'Douglas Murithi Kathuri is a regional EHSS and sustainability professional with 9+ years across infrastructure, construction, energy, oil and gas, and industry in Africa.',
        ];

        $content = json_decode((string) $section->content, true);

        if (is_array($content)) {
            $updates = [
                'content' => json_encode($this->appendProfile($content, $profile), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ];

            if (filled($section->draft_content)) {
                $draftContent = json_decode((string) $section->draft_content, true);

                if (is_array($draftContent)) {
                    $updates['draft_content'] = json_encode($this->appendProfile($draftContent, $profile), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            }

            DB::table('page_sections')->where('id', $section->id)->update($updates);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Preserve the published team profile and its public URL when rolling back.
    }

    /**
     * @param  array<string, mixed>  $content
     * @param  array<string, mixed>  $profile
     * @return array<string, mixed>
     */
    private function appendProfile(array $content, array $profile): array
    {
        $profiles = array_values(array_filter($content['profiles'] ?? [], 'is_array'));
        $profileAlreadyExists = collect($profiles)->contains(fn (array $existingProfile): bool => ($existingProfile['slug'] ?? null) === $profile['slug'] || ($existingProfile['name'] ?? null) === $profile['name']);

        if (! $profileAlreadyExists) {
            $profiles[] = $profile;
        }

        $content['profiles'] = $profiles;

        return $content;
    }
};
