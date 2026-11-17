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
            ->where('section_key', 'about_leadership')
            ->first(['id', 'content']);

        if ($section === null) {
            return;
        }

        $content = json_decode((string) $section->content, true);

        if (! is_array($content)) {
            return;
        }

        $defaults = [
            'network_introduction' => '<p>We draw on a wider network of experienced professionals and specialist consultants, bringing the right expertise to each project.</p>',
            'team_capabilities' => [
                ['title' => 'Project & programme management', 'description' => 'Planning, programme controls and delivery oversight.'],
                ['title' => 'Construction & site delivery', 'description' => 'Construction management, quality assurance and multidisciplinary coordination.'],
                ['title' => 'Technical & specialist advisory', 'description' => 'Technical interfaces, procurement and project specific specialist input.'],
            ],
            'team_explore_label' => 'Explore our experience',
            'team_explore_url' => '#professional-experience',
        ];
        $updated = false;

        foreach ($defaults as $field => $value) {
            $empty = is_array($value)
                ? empty($content[$field])
                : blank(strip_tags((string) ($content[$field] ?? '')));

            if (! array_key_exists($field, $content) || $empty) {
                $content[$field] = $value;
                $updated = true;
            }
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
        // Preserve wider team details if this data migration is rolled back.
    }
};
