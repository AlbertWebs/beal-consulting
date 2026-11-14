<?php

namespace Database\Seeders;

use App\Models\MediaAsset;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class WebsiteContentSeeder extends Seeder
{
    public function run(): void
    {
        $mediaByPath = [];
        $assetDisk = Storage::disk('assets');
        $knownPaths = collect(config('cms.sections'))->pluck('image')->filter()->unique();

        foreach (config('beal.services') as $service) {
            $knownPaths->push($service['image']);
        }

        $knownPaths->push('assets/beal-horizontal-logo.png', 'assets/beal-logo.png', 'assets/favicon.png');

        foreach ($knownPaths->unique() as $path) {
            $relativePath = str_starts_with($path, 'assets/') ? substr($path, 7) : $path;

            if (! $assetDisk->exists($relativePath)) {
                continue;
            }

            $absolutePath = $assetDisk->path($relativePath);
            $dimensions = @getimagesize($absolutePath) ?: [null, null, 'mime' => 'application/octet-stream'];
            $asset = MediaAsset::query()->firstOrCreate(
                ['disk' => 'assets', 'path' => $relativePath],
                [
                    'original_name' => basename($relativePath),
                    'mime_type' => $dimensions['mime'] ?? 'application/octet-stream',
                    'file_size' => $assetDisk->size($relativePath),
                    'width' => $dimensions[0],
                    'height' => $dimensions[1],
                    'alt_text' => '',
                    'is_system' => true,
                ],
            );
            $mediaByPath[$path] = $asset->id;
        }

        foreach (['home' => 'Home'] as $key => $title) {
            $page = WebsitePage::query()->firstOrCreate(
                ['page_key' => $key],
                ['title' => $title, 'slug' => $key === 'home' ? '/' : '/home-image', 'status' => $key === 'home' ? 'published' : 'draft', 'seo_title' => config('cms.settings.seo.title'), 'seo_description' => config('cms.settings.seo.description')],
            );

            if ($key === 'home') {
                foreach (config('cms.sections') as $sectionKey => $section) {
                    PageSection::query()->firstOrCreate(
                        ['website_page_id' => $page->id, 'section_key' => $sectionKey],
                        ['label' => $section['label'], 'content' => $section['defaults'], 'media_asset_id' => $mediaByPath[$section['image'] ?? ''] ?? null, 'is_enabled' => true, 'sort_order' => $section['position']],
                    );
                }
            }
        }

        foreach (config('beal.services') as $service) {
            WebsiteService::query()->firstOrCreate(
                ['slug' => $service['slug']],
                [
                    'number' => $service['number'], 'title' => $service['title'], 'description' => $service['description'],
                    'description_html' => '<p>'.$service['description'].'</p>', 'image_path' => $service['image'],
                    'media_asset_id' => $mediaByPath[$service['image']] ?? null, 'image_alt' => $service['image_alt'],
                    'subservices' => $service['subservices'], 'content' => config('cms.service_page_defaults'),
                    'status' => 'published', 'sort_order' => (int) $service['number'],
                    'seo_title' => $service['title'].' | BEAL Consulting', 'seo_description' => $service['description'],
                ],
            );
        }

        foreach (config('cms.settings') as $group => $settings) {
            foreach ($settings as $key => $value) {
                SiteSetting::query()->firstOrCreate(
                    ['setting_key' => $group.'.'.$key],
                    ['setting_group' => $group, 'value' => ['value' => $value]],
                );
            }
        }
    }
}
