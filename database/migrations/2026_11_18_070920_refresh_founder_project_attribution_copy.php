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
        $this->replaceCopy(
            'These projects reflect the Founding Director’s professional experience gained through previous engagements. They are not presented as projects directly contracted by BEAL Consulting Ltd.',
            'These projects reflect experience our team members gained in earlier roles. We bring that experience to our work at BEAL today.',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->replaceCopy(
            'These projects reflect experience our team members gained in earlier roles. We bring that experience to our work at BEAL today.',
            'These projects reflect the Founding Director’s professional experience gained through previous engagements. They are not presented as projects directly contracted by BEAL Consulting Ltd.',
        );
    }

    private function replaceCopy(string $oldCopy, string $newCopy): void
    {
        $aboutPageId = DB::table('website_pages')->where('page_key', 'about')->value('id');
        if ($aboutPageId === null) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('website_page_id', $aboutPageId)
            ->where('section_key', 'about_experience')
            ->first(['id', 'content']);
        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);
        if (! is_array($content) || ! is_string($content['attribution_note'] ?? null)) {
            return;
        }

        if (trim(strip_tags($content['attribution_note'])) !== $oldCopy) {
            return;
        }

        $content['attribution_note'] = '<p>'.$newCopy.'</p>';

        DB::table('page_sections')->where('id', $section->id)->update([
            'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }
};
