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

        if (! is_array($content) || ! is_array($content['profiles'] ?? null)) {
            return;
        }

        $members = [
            [
                'name' => 'Mark Kang\'ethe',
                'image' => '/assets/team/mark-kangethe-avatar.jpg',
                'image_alt' => 'Illustrated avatar for Mark Kang\'ethe',
            ],
            [
                'name' => 'Loise Manase',
                'image' => '/assets/team/loise-manase-avatar.jpg',
                'image_alt' => 'Illustrated avatar for Loise Manase',
            ],
            [
                'name' => 'Noel Kibet',
                'image' => '/assets/team/noel-kibet-avatar.jpg',
                'image_alt' => 'Illustrated avatar for Noel Kibet',
            ],
        ];
        $updated = false;

        foreach ($members as $member) {
            $index = array_search($member['name'], array_column($content['profiles'], 'name'), true);

            if ($index === false) {
                $content['profiles'][] = [...$member, 'role' => '', 'bio' => []];
                $updated = true;

                continue;
            }

            if (blank((string) ($content['profiles'][$index]['image'] ?? ''))) {
                $content['profiles'][$index]['image'] = $member['image'];
                $updated = true;
            }

            $genericAlt = 'Portrait of '.$member['name'];
            if (blank((string) ($content['profiles'][$index]['image_alt'] ?? '')) || ($content['profiles'][$index]['image_alt'] ?? '') === $genericAlt) {
                $content['profiles'][$index]['image_alt'] = $member['image_alt'];
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
        // Preserve the team avatars if this data migration is rolled back.
    }
};
