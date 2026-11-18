<?php

namespace Database\Seeders;

use App\Models\ContentEntry;
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
        $projectGalleryAssets = collect(config('cms.project_gallery_assets'))->keyBy('path');
        $knownPaths = collect(config('cms.sections'))->merge(config('cms.page_sections'))->pluck('image')->filter()->unique();

        foreach (config('beal.services') as $service) {
            $knownPaths->push($service['image']);
        }

        $knownPaths->push('assets/beal-horizontal-logo.png', 'assets/beal-logo.png', 'assets/favicon.png');
        $projectGalleryAssets->each(fn (array $asset) => $knownPaths->push('assets/'.$asset['path']));

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
                    'alt_text' => $projectGalleryAssets->get($relativePath)['alt_text'] ?? '',
                    'is_system' => true,
                ],
            );
            $mediaByPath[$path] = $asset->id;
        }

        $sitePages = [
            'home' => ['title' => 'Home', 'slug' => '/', 'navigation' => false, 'sections' => config('cms.sections')],
            'about' => ['title' => 'About BEAL', 'slug' => 'about', 'navigation' => false, 'description' => 'Learn about our Kenya-founded, Africa-focused built-environment and project advisory consultancy and how we support projects across the lifecycle.', 'sections' => ['about_overview' => config('cms.page_sections.about_overview'), 'about_approach' => config('cms.page_sections.about_approach'), 'about_profile' => config('cms.page_sections.about_profile'), 'about_leadership' => config('cms.page_sections.about_leadership'), 'about_experience' => config('cms.page_sections.about_experience')]],
            'services' => ['title' => 'Services', 'slug' => 'services', 'navigation' => false, 'description' => 'Explore BEAL Consulting’s five service pillars and capabilities across the project lifecycle.', 'sections' => ['services_intro' => config('cms.page_sections.services_intro')]],
            'sectors' => ['title' => 'Sectors', 'slug' => 'sectors', 'navigation' => false, 'description' => 'Explore the six areas of intended sector coverage identified in the BEAL Consulting website brief.', 'sections' => ['sectors_intro' => config('cms.page_sections.sectors_intro')]],
            'insights' => ['title' => 'Insights & Intelligence', 'slug' => 'insights', 'navigation' => false, 'description' => 'BEAL insights on infrastructure intelligence, project delivery, research, analysis and sector perspectives.', 'sections' => ['insights_intro' => config('cms.page_sections.insights_intro')]],
            'experience' => ['title' => 'Projects & Experience', 'slug' => 'experience', 'navigation' => false, 'description' => 'Verified BEAL engagements and previous professional experience, clearly attributed and presented with context.', 'sections' => ['experience_intro' => config('cms.page_sections.experience_intro')]],
            'contact' => ['title' => 'Contact', 'slug' => 'contact', 'navigation' => false, 'description' => 'Contact BEAL Consulting in Nairobi, Kenya, for a professional project or advisory enquiry.', 'sections' => ['contact_intro' => config('cms.page_sections.contact_intro')]],
        ];

        foreach ($sitePages as $key => $pageDefinition) {
            $title = $pageDefinition['title'];
            $page = WebsitePage::query()->firstOrCreate(
                ['page_key' => $key],
                ['title' => $title, 'slug' => $pageDefinition['slug'], 'status' => 'published', 'show_in_navigation' => $pageDefinition['navigation'], 'seo_title' => $title.' | BEAL Consulting', 'seo_description' => $pageDefinition['description'] ?? config('cms.settings.seo.description')],
            );

            foreach ($pageDefinition['sections'] as $sectionKey => $section) {
                PageSection::query()->firstOrCreate(
                    ['website_page_id' => $page->id, 'section_key' => $sectionKey],
                    ['label' => $section['label'], 'content' => $section['defaults'], 'media_asset_id' => $mediaByPath[$section['image'] ?? ''] ?? null, 'is_enabled' => true, 'sort_order' => $section['position']],
                );
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

        $sectors = [
            'real-estate-property-development' => ['Real estate & property development', 'We connect strategic research, feasibility and development advice with project governance and analytical support to inform property development decisions.'],
            'infrastructure-transport' => ['Infrastructure & transport', 'We support infrastructure and transport projects with development advisory, project management, governance, risk management and decision support.'],
            'commercial-institutional-development' => ['Commercial & institutional developments', 'We connect strategic advice, project delivery, governance and technical coordination to support commercial and institutional developments.'],
            'industrial-manufacturing' => ['Industrial & manufacturing', 'We bring development advisory, project controls, technical coordination, performance monitoring and analytical tools to complex industrial projects.'],
            'energy-utilities' => ['Energy & utilities', 'We support energy and utilities projects with feasibility and development advice, project intelligence, governance, risk management and performance analytics.'],
            'public-sector-development-programmes' => ['Public sector & development programmes', 'We support public sector and development programmes with programme management, portfolio oversight, monitoring, reporting and decision support.'],
        ];

        foreach ($sectors as $slug => [$title, $summary]) {
            ContentEntry::query()->firstOrCreate(
                ['type' => 'sector', 'slug' => $slug],
                ['title' => $title, 'summary' => $summary, 'content_html' => '<p>'.e($summary).'</p><p>Explore how we connect strategic advice, project management and governance to support informed decisions from early planning through delivery.</p>', 'status' => 'published', 'sort_order' => (array_search($slug, array_keys($sectors), true) + 1) * 10, 'seo_title' => $title.' | BEAL Consulting', 'seo_description' => $summary],
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
