<?php

namespace App\Http\Controllers;

use App\Models\ContentEntry;
use App\Models\MediaAsset;
use App\Models\PageSection;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use App\Services\WebsiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function partnerships(WebsiteSettings $websiteSettings): View
    {
        $settings = $websiteSettings->all();
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');

        return view('pages.partnerships', [
            'settings' => $settings,
            'isPreview' => false,
            'canonicalUrl' => $siteUrl.'/partnerships',
            'pageTitle' => 'Partnerships | Project Delivery Across Africa | BEAL Consulting',
            'pageDescription' => 'Explore ways to work with BEAL Consulting, from local delivery collaboration and specialist expertise to project and development partnerships across Africa.',
            'bannerImage' => asset('assets/home-image/nairobi-skyline.jpg'),
        ]);
    }

    public function scorecardAssessment(WebsiteSettings $websiteSettings): View
    {
        $settings = $websiteSettings->all();
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
        $banner = $settings['banners.scorecard_assessment'] ?? null;

        return view('pages.scorecard-assessment', [
            'settings' => $settings,
            'isPreview' => false,
            'robots' => 'noindex,nofollow',
            'canonicalUrl' => $siteUrl.'/project-health-scorecard/request',
            'pageTitle' => 'Request a Project Health Assessment | BEAL Consulting',
            'pageDescription' => 'Share a few details about your project and request a BEAL Project Health Scorecard assessment.',
            'bannerImage' => filled($banner) ? $banner : asset('assets/home-image/project-analytics.jpg'),
        ]);
    }

    public function scorecardSamples(WebsiteSettings $websiteSettings): View
    {
        $settings = $websiteSettings->all();
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
        $banner = $settings['banners.scorecard_samples'] ?? null;

        return view('pages.scorecard-samples', [
            'settings' => $settings,
            'isPreview' => false,
            'canonicalUrl' => $siteUrl.'/project-health-scorecard/samples',
            'pageTitle' => 'Construction Project Health Scorecards | BEAL Consulting',
            'pageDescription' => 'Explore interactive construction project health scorecards for cost, schedule, quality, safety, resources and risk. See how project performance monitoring supports better decisions.',
            'bannerImage' => filled($banner) ? $banner : asset('assets/home-image/project-analytics.jpg'),
        ]);
    }

    public function teamMember(string $slug, WebsiteSettings $websiteSettings): View
    {
        $aboutPage = WebsitePage::query()->with('sections.media')->where('page_key', 'about')->firstOrFail();
        abort_unless($aboutPage->isPublished(), 404);
        $leadership = $aboutPage->sections->firstWhere('section_key', 'about_leadership');
        abort_unless($leadership?->is_enabled, 404);
        $profiles = $leadership?->content['profiles'] ?? [];
        $profile = collect($profiles)->first(function (array $candidate) use ($slug): bool {
            $candidateSlug = filled($candidate['slug'] ?? null)
                ? Str::slug($candidate['slug'])
                : Str::slug((string) ($candidate['name'] ?? ''));

            return $candidateSlug === $slug;
        });
        abort_unless(is_array($profile) && filled($profile['name'] ?? ''), 404);

        $name = (string) $profile['name'];
        $role = trim((string) ($profile['role'] ?? 'BEAL team member'));
        $bio = is_array($profile['bio'] ?? null) ? array_values(array_filter($profile['bio'], 'is_string')) : [strip_tags((string) ($profile['bio'] ?? ''))];
        $bio = array_values(array_filter($bio, fn (string $paragraph): bool => filled(trim($paragraph))));
        $settings = $websiteSettings->all();
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
        $teamBanner = $settings['banners.team_profile'] ?? null;
        $profileDescription = trim((string) ($profile['seo_description'] ?? ''));
        $pageDescription = $profileDescription !== ''
            ? $profileDescription
            : $name.' is '.$role.' at BEAL Consulting, supporting project and built-environment advisory across Kenya and Africa.';

        return view('pages.team-member', [
            'profile' => $profile,
            'profileBio' => $bio,
            'profileSlug' => $slug,
            'profileRole' => $role,
            'settings' => $settings,
            'isPreview' => false,
            'canonicalUrl' => $siteUrl.'/team/'.$slug,
            'pageTitle' => filled($profile['seo_title'] ?? '') ? $profile['seo_title'] : $name.' | '.$role.' | BEAL Consulting',
            'pageDescription' => $pageDescription,
            'personJsonLd' => json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Person',
                'name' => $name,
                'jobTitle' => $role,
                'url' => $siteUrl.'/team/'.$slug,
                'worksFor' => ['@type' => 'Organization', 'name' => $settings['general.legal_name'] ?? 'BEAL Consulting Ltd'],
                ...(filled($profile['image'] ?? '') ? ['image' => url($profile['image'])] : []),
            ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
            'bannerImage' => filled($teamBanner) ? $teamBanner : asset('assets/home-image/glass-architecture.jpg'),
        ]);
    }

    public function show(string $pageKey, Request $request, WebsiteSettings $websiteSettings): View
    {
        abort_unless(in_array($pageKey, ['about', 'services', 'sectors', 'insights', 'experience', 'contact'], true), 404);
        $isPreview = $request->boolean('cms_preview');
        abort_if($isPreview && ! $request->user()?->canManageCms('pages'), 404);
        $page = WebsitePage::query()->with(['sections.media', 'sections.draftMedia'])->where('page_key', $pageKey)->firstOrFail();
        abort_if(! $isPreview && ! $page->isPublished(), 404);
        $sections = $page->sections->keyBy('section_key');
        $content = $sections->map(fn (PageSection $section): array => $isPreview ? ($section->draft_content ?? $section->content ?? []) : ($section->content ?? []))->all();
        $visible = $sections->mapWithKeys(fn (PageSection $section): array => [$section->section_key => $isPreview ? ($section->draft_enabled ?? $section->is_enabled) : $section->is_enabled])->all();
        $sectionOrders = $sections->mapWithKeys(fn (PageSection $section): array => [$section->section_key => $section->sort_order])->all();
        $settings = $websiteSettings->all();
        $pageBannerSlots = [
            'about_overview' => 'page_about',
            'services_intro' => 'page_services',
            'sectors_intro' => 'page_sectors',
            'insights_intro' => 'page_insights',
            'experience_intro' => 'page_experience',
            'contact_intro' => 'page_contact',
        ];
        $sectionImages = [];
        foreach (config('cms.page_sections') as $key => $schema) {
            $section = $sections->get($key);
            $media = $isPreview ? ($section?->draftMedia ?? $section?->media) : $section?->media;
            $banner = isset($pageBannerSlots[$key]) ? ($settings['banners.'.$pageBannerSlots[$key]] ?? null) : null;
            $sectionImages[$key] = filled($banner) ? $banner : ($media?->url ?? asset($schema['image'] ?? 'assets/home-image/glass-architecture.jpg'));
        }
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
        $contentType = match ($pageKey) {
            'sectors' => 'sector',
            'insights' => 'insight',
            'experience' => 'project',
            default => null,
        };
        $services = WebsiteService::query()->with('media')->where('status', 'published')->orderBy('sort_order')->get();
        $services->each(function (WebsiteService $service) use ($settings): void {
            $service->setAttribute('banner_url', $settings['banners.service_'.$service->id] ?? $service->media?->url ?? asset(ltrim((string) $service->image_path, '/')));
        });
        $entryQuery = ContentEntry::query()->with('media')->where('type', $contentType)->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()));
        $search = trim((string) $request->query('q', ''));
        $category = trim((string) $request->query('category', ''));
        if ($pageKey === 'insights') {
            if ($search !== '') {
                $entryQuery->where(fn ($query) => $query->where('title', 'like', '%'.$search.'%')->orWhere('summary', 'like', '%'.$search.'%')->orWhere('category', 'like', '%'.$search.'%'));
            }
            if ($category !== '') {
                $entryQuery->where('category', $category);
            }
        }
        $entries = $contentType === null ? collect() : $entryQuery->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('published_at')->get();
        $entries->each(function (ContentEntry $entry) use ($settings): void {
            $entry->setAttribute('banner_url', $settings['banners.entry_'.$entry->id] ?? $entry->media?->url);
        });
        $categories = $pageKey === 'insights' ? ContentEntry::query()->where('type', 'insight')->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->whereNotNull('category')->distinct()->orderBy('category')->pluck('category') : collect();
        $experienceProjectLinks = collect();
        if ($pageKey === 'about') {
            $projectTitles = array_values(array_filter($content['about_experience']['projects'] ?? [], 'is_string'));
            if ($projectTitles !== []) {
                $experienceProjectLinks = ContentEntry::query()
                    ->where('type', 'project')
                    ->where('status', 'published')
                    ->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))
                    ->whereIn('title', $projectTitles)
                    ->get(['id', 'title', 'slug'])
                    ->keyBy('title');
            }
        }

        return view('pages.'.$pageKey, [
            'page' => $page,
            'content' => $content,
            'visible' => $visible,
            'sectionOrders' => $sectionOrders,
            'sectionImages' => $sectionImages,
            'entries' => $entries,
            'experienceProjectLinks' => $experienceProjectLinks,
            'categories' => $categories,
            'search' => $search,
            'selectedCategory' => $category,
            'settings' => $settings,
            'isPreview' => $isPreview,
            'canonicalUrl' => $siteUrl.'/'.$page->slug,
            'pageTitle' => $page->seo_title ?: $page->title.' | BEAL Consulting',
            'pageDescription' => $page->seo_description ?: ($settings['seo.description'] ?? ''),
            'serviceOptions' => $services,
            'relatedEntries' => collect(),
            'services' => $services,
        ]);
    }

    public function entry(string $type, string $slug, Request $request, WebsiteSettings $websiteSettings): View
    {
        abort_unless(in_array($type, ['sector', 'insight', 'project'], true), 404);
        $isPreview = $request->boolean('cms_preview');
        abort_if($isPreview && ! $request->user()?->canManageCms('content'), 404);
        $entry = ContentEntry::query()->with('media')->where('type', $type)->where('slug', $slug)
            ->when(! $isPreview, fn ($query) => $query->where('status', 'published'))
            ->when(! $isPreview, fn ($query) => $query->where(fn ($dateQuery) => $dateQuery->whereNull('published_at')->orWhere('published_at', '<=', now())))
            ->firstOrFail();
        if ($isPreview && is_array($entry->draft_data)) {
            $entry->forceFill($entry->draft_data);
            $entry->unsetRelation('media')->load('media');
        }

        $settings = $websiteSettings->all();
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
        $entryBanner = $settings['banners.entry_'.$entry->id] ?? null;
        $routePath = match ($type) {
            'sector' => 'sectors',
            'insight' => 'insights',
            'project' => 'experience',
        };
        $relatedEntries = $type === 'insight' ? ContentEntry::query()->where('type', 'insight')->where('status', 'published')->where('id', '!=', $entry->id)->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->when($entry->category, fn ($query) => $query->where('category', $entry->category))->orderByDesc('published_at')->limit(3)->get() : collect();
        $galleryMediaIds = $type === 'project' ? ($entry->gallery_media_ids ?? []) : [];
        $galleryMedia = $galleryMediaIds === []
            ? collect()
            : MediaAsset::query()->whereIn('id', $galleryMediaIds)->get()->sortBy(fn (MediaAsset $mediaAsset): int|false => array_search($mediaAsset->id, $galleryMediaIds))->values();
        $projectGalleryStages = config('cms.project_gallery_stages', []);
        $galleryMedia->each(function (MediaAsset $mediaAsset) use ($entry, $projectGalleryStages): void {
            $stage = $entry->gallery_stages[$mediaAsset->id] ?? 'general';
            if (! array_key_exists($stage, $projectGalleryStages)) {
                $stage = 'general';
            }
            $mediaAsset->setAttribute('project_stage', $stage);
            $mediaAsset->setAttribute('project_stage_label', $projectGalleryStages[$stage]);
        });
        $galleryMedia = $galleryMedia->sortBy(fn (MediaAsset $mediaAsset): array => [
            array_search($mediaAsset->project_stage, array_keys($projectGalleryStages), true),
            array_search($mediaAsset->id, $galleryMediaIds, true),
        ])->values();

        return view('pages.entry', [
            'entry' => $entry,
            'type' => $type,
            'routePath' => $routePath,
            'settings' => $settings,
            'isPreview' => $isPreview,
            'canonicalUrl' => $siteUrl.'/'.$routePath.'/'.$entry->slug,
            'pageTitle' => $entry->seo_title ?: $entry->title.' | BEAL Consulting',
            'pageDescription' => $entry->seo_description ?: ($entry->summary ?? ''),
            'serviceOptions' => WebsiteService::query()->where('status', 'published')->orderBy('sort_order')->get(),
            'services' => WebsiteService::query()->where('status', 'published')->orderBy('sort_order')->get(),
            'relatedEntries' => $relatedEntries,
            'galleryMedia' => $galleryMedia,
            'projectGalleryStages' => $projectGalleryStages,
            'bannerImage' => filled($entryBanner) ? $entryBanner : ($entry->media?->url ?? asset('assets/home-image/'.($type === 'sector' ? 'urban-infrastructure.jpg' : ($type === 'insight' ? 'nairobi-skyline.jpg' : 'city-development.jpg')))),
        ]);
    }

    public function sector(string $slug, Request $request, WebsiteSettings $websiteSettings): View
    {
        return $this->entry('sector', $slug, $request, $websiteSettings);
    }

    public function insight(string $slug, Request $request, WebsiteSettings $websiteSettings): View
    {
        return $this->entry('insight', $slug, $request, $websiteSettings);
    }

    public function project(string $slug, Request $request, WebsiteSettings $websiteSettings): View
    {
        return $this->entry('project', $slug, $request, $websiteSettings);
    }
}
