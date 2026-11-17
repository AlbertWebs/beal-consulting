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

        $members = [
            [
                'name' => 'Mark Kang\'ethe',
                'role' => 'Business Development',
                'image' => '',
                'image_alt' => 'Portrait of Mark Kang\'ethe',
                'bio' => [],
            ],
            [
                'name' => 'Loise Manase',
                'role' => 'Structural Engineer',
                'image' => '',
                'image_alt' => 'Portrait of Loise Manase',
                'bio' => [],
            ],
            [
                'name' => 'Noel Kibet',
                'role' => 'Quantity Surveyor (QS)',
                'image' => '',
                'image_alt' => 'Portrait of Noel Kibet',
                'bio' => [],
            ],
        ];

        $profiles = array_values(array_filter($content['profiles'] ?? [], 'is_array'));

        foreach ($members as $member) {
            $index = array_search($member['name'], array_column($profiles, 'name'), true);

            if ($index === false) {
                $profiles[] = $member;

                continue;
            }

            foreach ($member as $field => $value) {
                if (! array_key_exists($field, $profiles[$index]) || ($field === 'role' && blank($profiles[$index][$field]))) {
                    $profiles[$index][$field] = $value;
                }
            }
        }

        $content['profiles'] = $profiles;
        $content['network_introduction'] = '<p>Meet the wider team contributing across business development, structural engineering and quantity surveying.</p>';
        $content['team_explore_label'] = $content['team_explore_label'] ?? 'Explore our experience';
        $content['team_explore_url'] = $content['team_explore_url'] ?? '#professional-experience';
        unset($content['team_capabilities'], $content['team_capabilities_json']);

        DB::table('page_sections')->where('id', $section->id)->update([
            'content' => json_encode($content, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Keep the published team member profiles when this data migration is rolled back.
    }
};
