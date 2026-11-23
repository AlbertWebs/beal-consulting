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
            'name' => 'Lydiah Kanyiri',
            'slug' => 'lydiah-kanyiri',
            'role' => 'Construction Project Manager',
            'image' => '/assets/team/lydiah-kanyiri.jpeg',
            'image_alt' => 'Portrait of Lydiah Kanyiri, Construction Project Manager',
            'introduction' => 'A construction project manager with a track record of taking on complex, delayed and high-pressure developments and driving them through to completion.',
            'bio' => [
                'Lydiah Kanyiri is a Construction Project Manager with more than 10 years of experience delivering projects across Kenya. She has worked on stalled, distressed and high-pressure developments in the residential, commercial, industrial and institutional sectors, with project values ranging from hundreds of millions to tens of billions of shillings.',
                'Her strength is running projects: setting the programme, planning the work and keeping project teams aligned and moving. She is equally comfortable building a delivery plan from the ground up or stepping into a project that has gone off track. Lydiah holds a Bachelor of Science in Construction Management from the University of Nairobi and a postgraduate certificate in Construction Engineering Management from the University of East London.',
            ],
            'specialisms' => [
                'Construction project and programme management',
                'Recovery of stalled and distressed projects',
                'Project planning and programme development',
                'Team coordination and delivery oversight',
                'Residential, commercial, industrial and institutional developments',
            ],
            'sections' => [[
                'heading' => 'Getting complex projects moving',
                'paragraphs' => [
                    'Lydiah has managed projects in demanding conditions, including developments that were delayed, under pressure or in need of a clearer route to completion. She brings structure to the work by establishing practical programmes, clarifying priorities and keeping project teams focused on delivery.',
                    'Her experience spans projects valued from hundreds of millions to tens of billions of shillings across Kenya, covering residential, commercial, industrial and institutional developments.',
                ],
            ], [
                'heading' => 'Education',
                'body' => 'Lydiah holds a BSc in Construction Management from the University of Nairobi and a postgraduate certificate in Construction Engineering Management from the University of East London.',
            ]],
            'seo_title' => 'Lydiah Kanyiri | Construction Project Manager | BEAL Consulting',
            'seo_description' => 'Meet Lydiah Kanyiri, a construction project manager with 10+ years delivering complex and distressed developments across Kenya.',
        ];

        $updates = [];
        $content = json_decode((string) $section->content, true);

        if (is_array($content)) {
            $updates['content'] = json_encode($this->appendProfile($content, $profile), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        if (filled($section->draft_content)) {
            $draftContent = json_decode((string) $section->draft_content, true);

            if (is_array($draftContent)) {
                $updates['draft_content'] = json_encode($this->appendProfile($draftContent, $profile), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }
        }

        if ($updates !== []) {
            $updates['updated_at'] = now();
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
