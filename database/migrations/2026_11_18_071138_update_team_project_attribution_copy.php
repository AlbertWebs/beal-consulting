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
            'Benson worked on these projects in earlier roles, and that experience now informs his work at BEAL. They were delivered through those earlier engagements, not by BEAL Consulting.',
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
            'Benson worked on these projects in earlier roles, and that experience now informs his work at BEAL. They were delivered through those earlier engagements, not by BEAL Consulting.',
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
