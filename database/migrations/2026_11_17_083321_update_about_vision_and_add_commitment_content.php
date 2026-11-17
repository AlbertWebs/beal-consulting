<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $pageId = DB::table('website_pages')->where('page_key', 'about')->value('id');

        if ($pageId === null) {
            return;
        }

        $section = DB::table('page_sections')
            ->where('website_page_id', $pageId)
            ->where('section_key', 'about_profile')
            ->first(['id', 'content']);

        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);

        if (! is_array($content)) {
            return;
        }

        $previousVision = '<p>To build a trusted African consultancy that supports better project outcomes through analytical capability, technical competence and forward-looking advisory.</p>';
        $newVision = '<p>To become a trusted African project advisory and built-environment consultancy, recognised for professional excellence, effective project governance and the successful delivery of sustainable development initiatives.</p>';
        $commitment = '<p>We are committed to bringing clarity to complex projects, strengthening accountability and helping clients translate development objectives into measurable project outcomes.</p>';
        $updated = false;

        if (blank(strip_tags((string) ($content['vision'] ?? ''))) || ($content['vision'] ?? null) === $previousVision) {
            $content['vision'] = $newVision;
            $updated = true;
        }

        if (blank(strip_tags((string) ($content['commitment'] ?? '')))) {
            $content['commitment'] = $commitment;
            $updated = true;
        }

        if ($updated) {
            DB::table('page_sections')->where('id', $section->id)->update([
                'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Preserve the published vision and commitment if this data migration is rolled back.
    }
};
