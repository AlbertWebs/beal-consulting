<?php

namespace Database\Seeders;

use App\Models\ContentEntry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FounderProjectDraftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $projects = [
            'Itare Fresh Water Dam Project',
            'Onyx Apartments',
            'East African Breweries Limited (EABL) Headquarters Fit-out',
            'Scania East Africa Facilities',
            'EABL Microbrewery Project',
            'Africa Area Office Development',
            'British American Tobacco (BAT) Headquarters Refurbishment',
        ];

        foreach ($projects as $index => $title) {
            $slug = Str::slug($title);
            $draftData = [
                'title' => $title,
                'slug' => $slug,
                'summary' => '',
                'content_html' => '',
                'category' => '',
                'author' => '',
                'location' => '',
                'scale' => '',
                'scope' => '',
                'outcomes' => '',
                'experience_source' => 'previous_professional',
                'media_asset_id' => null,
                'gallery_media_ids' => [],
                'image_alt' => '',
                'seo_title' => '',
                'seo_description' => '',
                'sort_order' => ($index + 1) * 10,
                'is_featured' => false,
                'published_at' => null,
            ];

            ContentEntry::query()->firstOrCreate(
                ['type' => 'project', 'slug' => $slug],
                [
                    ...$draftData,
                    'title' => $title,
                    'status' => 'draft',
                    'draft_data' => $draftData,
                ],
            );
        }
    }
}
