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
        $knownPaths = collect(config('cms.sections'))->merge(config('cms.page_sections'))->pluck('image')->filter()->unique();

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

        $sitePages = [
            'home' => ['title' => 'Home', 'slug' => '/', 'navigation' => false, 'sections' => config('cms.sections')],
            'about' => ['title' => 'About BEAL', 'slug' => 'about', 'navigation' => false, 'description' => 'Learn about BEAL Consulting Ltd, an intelligence-led strategic advisory and infrastructure consultancy serving East Africa and the wider African market.', 'sections' => ['about_overview' => config('cms.page_sections.about_overview'), 'about_profile' => config('cms.page_sections.about_profile'), 'about_leadership' => config('cms.page_sections.about_leadership')]],
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
            'real-estate-property-development' => ['Real estate & property development', 'Property development decisions connect strategic research, feasibility and development advisory, project governance and analytical decision support.'],
            'infrastructure-transport' => ['Infrastructure & transport', 'BEAL identifies infrastructure and transport as an intended sector focus. Its stated services include infrastructure development advisory, project management, governance, risk and decision support.'],
            'commercial-institutional-development' => ['Commercial & institutional developments', 'BEAL’s brief includes commercial and institutional developments in its intended sector coverage. Its service offer connects strategic advice, project delivery, governance and technical coordination.'],
            'industrial-manufacturing' => ['Industrial & manufacturing', 'BEAL’s stated capabilities include development advisory, project controls, technical coordination, performance monitoring and analytical tools relevant to complex industrial projects.'],
            'energy-utilities' => ['Energy & utilities', 'Energy and utilities form part of BEAL’s intended sector coverage. The service offer includes feasibility and development advisory, project intelligence, governance, risk management and performance analytics.'],
            'public-sector-development-programmes' => ['Public sector & development programmes', 'BEAL’s brief identifies public sector and development programmes among its intended sectors. Relevant capabilities include programme management, portfolio oversight, monitoring and reporting, and decision support.'],
        ];

        foreach ($sectors as $slug => [$title, $summary]) {
            ContentEntry::query()->firstOrCreate(
                ['type' => 'sector', 'slug' => $slug],
                ['title' => $title, 'summary' => $summary, 'content_html' => '<p>'.e($summary).'</p><p>This sector overview describes BEAL’s stated service offer and intended coverage. It is not a claim of a specific completed engagement.</p>', 'status' => 'published', 'sort_order' => (array_search($slug, array_keys($sectors), true) + 1) * 10, 'seo_title' => $title.' | BEAL Consulting', 'seo_description' => $summary],
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
