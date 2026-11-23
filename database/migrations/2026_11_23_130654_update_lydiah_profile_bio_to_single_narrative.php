<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PROFILE_SLUG = 'lydiah-kanyiri';

    private const BIO = 'Lydiah Kanyiri is a Partner & Director and construction project manager with more than 10 years’ experience across Kenya, focused on planning, programme leadership and delivery of residential, commercial, industrial and institutional projects. She is often brought into stalled, distressed and high-pressure developments—projects valued from hundreds of millions to tens of billions of shillings—to establish a workable programme, set priorities and align teams around the steps needed to reach completion. She holds a BSc in Construction Management from the University of Nairobi and a postgraduate certificate in Construction Engineering Management from the University of East London.';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->updateProfile(fn (array $profile): array => [
            ...$profile,
            'introduction' => '',
            'bio' => [self::BIO],
            'specialisms' => [],
            'sections' => [],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->updateProfile(fn (array $profile): array => [
            ...$profile,
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
        ]);
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
