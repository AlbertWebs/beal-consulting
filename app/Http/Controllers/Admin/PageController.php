<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsRevision;
use App\Models\MediaAsset;
use App\Models\PageSection;
use App\Models\WebsitePage;
use App\Services\CmsAuditLogger;
use App\Services\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', ['pages' => WebsitePage::query()->with('updater')->orderBy('sort_order')->get()]);
    }

    public function edit(WebsitePage $websitePage): View
    {
        return view('admin.pages.edit', [
            'page' => $websitePage->load(['sections.media', 'sections.draftMedia', 'updater']),
            'schemas' => array_merge(config('cms.sections'), config('cms.page_sections')),
            'mediaAssets' => MediaAsset::query()->where('mime_type', 'like', 'image/%')->orderBy('original_name')->get(),
        ]);
    }

    public function revisions(WebsitePage $websitePage): View
    {
        $revisions = CmsRevision::query()->with('user')->where('revisable_type', $websitePage->getMorphClass())->where('revisable_id', $websitePage->id)->latest()->paginate(20);

        return view('admin.pages.revisions', ['page' => $websitePage, 'revisions' => $revisions]);
    }

    public function sectionRevisions(WebsitePage $websitePage, PageSection $section): View
    {
        abort_unless($section->website_page_id === $websitePage->id, 404);
        $revisions = CmsRevision::query()->with('user')->where('revisable_type', $section->getMorphClass())->where('revisable_id', $section->id)->latest()->paginate(20);

        return view('admin.pages.section-revisions', ['page' => $websitePage, 'section' => $section, 'revisions' => $revisions]);
    }

    public function restoreSectionRevision(Request $request, WebsitePage $websitePage, PageSection $section, CmsRevision $revision, CmsAuditLogger $audit): RedirectResponse
    {
        abort_unless($section->website_page_id === $websitePage->id && $revision->revisable_type === $section->getMorphClass() && $revision->revisable_id === $section->id, 404);
        CmsRevision::query()->create(['revisable_type' => $section->getMorphClass(), 'revisable_id' => $section->id, 'snapshot' => $section->toArray(), 'user_id' => $request->user()->id]);
        $snapshot = $revision->snapshot;
        $section->update([
            'draft_content' => $snapshot['draft_content'] ?? $snapshot['content'] ?? [],
            'draft_enabled' => $snapshot['draft_enabled'] ?? $snapshot['is_enabled'] ?? true,
            'draft_media_asset_id' => $snapshot['draft_media_asset_id'] ?? $snapshot['media_asset_id'] ?? null,
            'updated_by' => $request->user()->id,
        ]);
        $audit->record('section.revision_restored', 'Restored draft for '.$section->label, $section, ['revision_id' => $revision->id]);

        return to_route('admin.pages.edit', $websitePage)->with('success', 'Section revision restored to draft. Publish the page to make it live.');
    }

    public function restoreRevision(Request $request, WebsitePage $websitePage, CmsRevision $revision, CmsAuditLogger $audit): RedirectResponse
    {
        abort_unless($revision->revisable_type === $websitePage->getMorphClass() && $revision->revisable_id === $websitePage->id, 404);
        CmsRevision::query()->create(['revisable_type' => $websitePage->getMorphClass(), 'revisable_id' => $websitePage->id, 'snapshot' => $websitePage->toArray(), 'user_id' => $request->user()->id]);
        $websitePage->update(Arr::only($revision->snapshot, ['title', 'slug', 'status', 'navigation_label', 'show_in_navigation', 'sort_order', 'seo_title', 'seo_description', 'open_graph_media_id', 'published_at']));
        $audit->record('page.restored', 'Restored page revision: '.$websitePage->title, $websitePage, ['revision_id' => $revision->id]);

        return to_route('admin.pages.edit', $websitePage)->with('success', 'Page revision restored.');
    }

    public function publish(Request $request, WebsitePage $websitePage, CmsAuditLogger $audit): RedirectResponse
    {
        DB::transaction(function () use ($websitePage, $request): void {
            foreach ($websitePage->sections as $section) {
                if ($section->draft_content === null && $section->draft_enabled === null && $section->draft_media_asset_id === null) {
                    continue;
                }

                $section->update([
                    'content' => $section->draft_content ?? $section->content,
                    'is_enabled' => $section->draft_enabled ?? $section->is_enabled,
                    'media_asset_id' => $section->draft_media_asset_id ?? $section->media_asset_id,
                    'draft_content' => null,
                    'draft_enabled' => null,
                    'draft_media_asset_id' => null,
                    'updated_by' => $request->user()->id,
                ]);
            }

            $websitePage->update(['status' => 'published', 'published_at' => now(), 'updated_by' => $request->user()->id]);
        });

        $audit->record('page.published', 'Published homepage changes', $websitePage);

        return back()->with('success', 'Homepage changes are now live.');
    }

    public function update(Request $request, WebsitePage $websitePage, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:160', 'unique:website_pages,slug,'.$websitePage->id],
            'status' => ['required', 'in:draft,published,unpublished'],
            'navigation_label' => ['nullable', 'string', 'max:100'],
            'show_in_navigation' => ['sometimes', 'boolean'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:320'],
        ]);

        $data['show_in_navigation'] = $request->boolean('show_in_navigation');
        $data['updated_by'] = $request->user()->id;
        $data['published_at'] = $data['status'] === 'published' ? ($websitePage->published_at ?? now()) : null;
        CmsRevision::query()->create(['revisable_type' => $websitePage->getMorphClass(), 'revisable_id' => $websitePage->id, 'snapshot' => $websitePage->toArray(), 'user_id' => $request->user()->id]);
        $websitePage->update($data);
        $audit->record('page.updated', 'Updated page: '.$websitePage->title, $websitePage, ['status' => $websitePage->status]);

        return back()->with('success', 'Page settings saved.');
    }

    public function updateSection(Request $request, WebsitePage $websitePage, PageSection $section, RichTextSanitizer $sanitizer, CmsAuditLogger $audit): RedirectResponse
    {
        abort_unless($section->website_page_id === $websitePage->id, 404);
        $schema = config('cms.sections.'.$section->section_key) ?? config('cms.page_sections.'.$section->section_key);
        abort_unless(is_array($schema), 404);

        $rules = ['is_enabled' => ['sometimes', 'boolean'], 'media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'], 'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000']];

        foreach ($schema['fields'] as $key => $field) {
            $inputKey = $field['type'] === 'repeater' ? 'items_json' : $key;
            $rules['content.'.$inputKey] = match ($field['type']) {
                'richtext' => ['nullable', 'string', 'max:30000'],
                'url' => ['nullable', 'string', 'max:2048'],
                'repeater', 'json' => ['nullable', 'string', 'max:12000'],
                default => ['nullable', 'string', 'max:2000'],
            };
        }

        $data = $request->validate($rules);
        $content = array_merge($section->draft_content ?? $section->content ?? [], $data['content'] ?? []);

        foreach ($schema['fields'] as $key => $field) {
            if ($field['type'] === 'url' && isset($content[$key])) {
                abort_unless($this->isSafeUrl((string) $content[$key]), 422, 'Button links must use a relative path or http(s) URL.');
            }
        }

        if (isset($content['items_json'])) {
            $items = json_decode($content['items_json'], true);
            abort_unless(is_array($items), 422, 'Repeating items must contain valid JSON.');
            foreach ($items as $item) {
                abort_unless(is_array($item), 422, 'Each repeated item must be an object.');
                foreach (array_keys($schema['fields']['items']['columns']) as $column) {
                    abort_unless(! isset($item[$column]) || is_string($item[$column]), 422, 'Repeated item fields must be text.');
                }
                if (isset($item['url'])) {
                    abort_unless($this->isSafeUrl((string) $item['url']), 422, 'Repeating item links must use a relative path or http(s) URL.');
                }
            }
            $content['items'] = $items;
            unset($content['items_json']);
        }

        foreach ($schema['fields'] as $key => $field) {
            if ($field['type'] !== 'json' || ! array_key_exists($key, $content)) {
                continue;
            }
            $decoded = json_decode((string) $content[$key], true);
            abort_unless(is_array($decoded), 422, $field['label'].' must contain valid JSON.');
            if ($key === 'profiles_json') {
                foreach ($decoded as $profile) {
                    abort_unless(is_array($profile), 422, 'Each team member must be an object.');
                    foreach (['image', 'image_alt'] as $imageField) {
                        abort_unless(! isset($profile[$imageField]) || is_string($profile[$imageField]), 422, 'Team member image details must be text.');
                    }
                    abort_unless(strlen((string) ($profile['image_alt'] ?? '')) <= 500, 422, 'Image descriptions cannot exceed 500 characters.');
                    abort_unless($this->isSafeUrl((string) ($profile['image'] ?? '')), 422, 'Team member images must use a relative path or http(s) URL.');
                }
            }
            $targetKey = str_ends_with($key, '_json') ? substr($key, 0, -5) : $key;
            $content[$targetKey] = $decoded;
            unset($content[$key]);
        }

        foreach ($schema['fields'] as $key => $field) {
            if ($field['type'] === 'richtext' && array_key_exists($key, $content)) {
                $content[$key] = $sanitizer->clean($content[$key]);
            }
        }

        if (isset($content['items'])) {
            $content['items'] = array_values(array_map(fn (array $item): array => Arr::only($item, array_keys($schema['fields']['items']['columns'])), $content['items']));
        }

        if (! $request->boolean('autosave') && $request->header('X-CMS-Autosave') !== 'true') {
            CmsRevision::query()->create(['revisable_type' => $section->getMorphClass(), 'revisable_id' => $section->id, 'snapshot' => $section->toArray(), 'user_id' => $request->user()->id]);
        }
        $section->update(['draft_content' => $content, 'draft_media_asset_id' => $data['media_asset_id'] ?? null, 'draft_enabled' => $request->boolean('is_enabled'), 'sort_order' => $data['sort_order'] ?? $section->sort_order, 'updated_by' => $request->user()->id]);
        if ($request->header('X-CMS-Autosave') !== 'true') {
            $audit->record('section.updated', 'Updated '.$section->label.' section', $section);
        }

        return back()->with('success', $section->label.' section saved.');
    }

    public function reorderSections(Request $request, WebsitePage $websitePage, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate(['sections' => ['required', 'array'], 'sections.*' => ['required', 'integer', 'distinct', 'exists:page_sections,id']]);
        abort_unless(PageSection::query()->where('website_page_id', $websitePage->id)->whereIn('id', $data['sections'])->count() === count($data['sections']), 422);

        DB::transaction(function () use ($data, $websitePage): void {
            foreach ($data['sections'] as $index => $id) {
                PageSection::query()->where('website_page_id', $websitePage->id)->whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
            }
        });
        $audit->record('sections.reordered', 'Reordered sections for '.$websitePage->title, $websitePage);

        return back()->with('success', 'Section order saved.');
    }

    private function isSafeUrl(string $url): bool
    {
        return $url === '' || str_starts_with($url, '#') || str_starts_with($url, '/') && ! str_starts_with($url, '//') || filter_var($url, FILTER_VALIDATE_URL) !== false && in_array(parse_url($url, PHP_URL_SCHEME), ['http', 'https'], true);
    }
}
