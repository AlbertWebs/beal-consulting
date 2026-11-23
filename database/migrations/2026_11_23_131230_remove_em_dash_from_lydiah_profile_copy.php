<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const PROFILE_SLUG = 'lydiah-kanyiri';

    private const BIO = 'Lydiah Kanyiri is a Partner & Director and construction project manager with more than 10 years’ experience across Kenya, focused on planning, programme leadership and delivery of residential, commercial, industrial and institutional projects. She is often brought into stalled, distressed and high-pressure developments, with project values ranging from hundreds of millions to tens of billions of shillings. She establishes a workable programme, sets priorities and aligns teams around the steps needed to reach completion. She holds a BSc in Construction Management from the University of Nairobi and a postgraduate certificate in Construction Engineering Management from the University of East London.';

    private const PREVIOUS_BIO = "Lydiah Kanyiri is a Partner & Director and construction project manager with more than 10 years’ experience across Kenya, focused on planning, programme leadership and delivery of residential, commercial, industrial and institutional projects. She is often brought into stalled, distressed and high-pressure developments\u{2014}projects valued from hundreds of millions to tens of billions of shillings\u{2014}to establish a workable programme, set priorities and align teams around the steps needed to reach completion. She holds a BSc in Construction Management from the University of Nairobi and a postgraduate certificate in Construction Engineering Management from the University of East London.";

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
        $this->updateProfileBio(self::PREVIOUS_BIO);
    }

    private function updateProfileBio(string $bio): void
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
                    $profile['bio'] = [$bio];
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
