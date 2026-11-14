<?php

namespace App\Http\Controllers;

use App\Models\ContentEntry;
use App\Models\PageSection;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use App\Services\WebsiteSettings;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicPageController extends Controller
{
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
        $sectionImages = [];
        foreach (config('cms.page_sections') as $key => $schema) {
            $section = $sections->get($key);
            $media = $isPreview ? ($section?->draftMedia ?? $section?->media) : $section?->media;
            $sectionImages[$key] = $media?->url ?? asset($schema['image'] ?? 'assets/home-image/glass-architecture.jpg');
        }
        $settings = $websiteSettings->all();
        $siteUrl = rtrim((string) ($settings['general.site_url'] ?? config('beal.site_url')), '/');
        $contentType = match ($pageKey) {
            'sectors' => 'sector',
            'insights' => 'insight',
            'experience' => 'project',
            default => null,
        };
        $services = WebsiteService::query()->with('media')->where('status', 'published')->orderBy('sort_order')->get();
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
        $categories = $pageKey === 'insights' ? ContentEntry::query()->where('type', 'insight')->where('status', 'published')->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->whereNotNull('category')->distinct()->orderBy('category')->pluck('category') : collect();

        return view('pages.'.$pageKey, [
            'page' => $page,
            'content' => $content,
            'visible' => $visible,
            'sectionOrders' => $sectionOrders,
            'sectionImages' => $sectionImages,
            'entries' => $entries,
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
        $routePath = match ($type) {
            'sector' => 'sectors',
            'insight' => 'insights',
            'project' => 'experience',
        };
        $relatedEntries = $type === 'insight' ? ContentEntry::query()->where('type', 'insight')->where('status', 'published')->where('id', '!=', $entry->id)->where(fn ($query) => $query->whereNull('published_at')->orWhere('published_at', '<=', now()))->when($entry->category, fn ($query) => $query->where('category', $entry->category))->orderByDesc('published_at')->limit(3)->get() : collect();

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
