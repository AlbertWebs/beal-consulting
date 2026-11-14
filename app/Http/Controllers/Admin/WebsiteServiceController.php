<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsRevision;
use App\Models\MediaAsset;
use App\Models\WebsiteService;
use App\Services\CmsAuditLogger;
use App\Services\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WebsiteServiceController extends Controller
{
    public function index(): View
    {
        return view('admin.services.index', ['services' => WebsiteService::query()->orderBy('sort_order')->get()]);
    }

    public function create(): View
    {
        return view('admin.services.edit', ['service' => new WebsiteService, 'editData' => [], 'mediaAssets' => MediaAsset::query()->where('mime_type', 'like', 'image/%')->orderBy('original_name')->get()]);
    }

    public function store(Request $request, RichTextSanitizer $sanitizer, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $this->validated($request, $sanitizer);
        $service = WebsiteService::query()->create([
            ...Arr::except($data, ['content_json', 'subservices_text']),
            'draft_content' => Arr::only($data, ['number', 'title', 'slug', 'description', 'description_html', 'image_path', 'media_asset_id', 'image_alt', 'subservices', 'content', 'seo_title', 'seo_description']),
            'status' => 'draft',
            'updated_by' => $request->user()->id,
        ]);
        $audit->record('service.created', 'Created service draft: '.$service->title, $service);

        return to_route('admin.services.edit', $service)->with('success', 'Service draft created. Review it, then publish when ready.');
    }

    public function edit(WebsiteService $websiteService): View
    {
        return view('admin.services.edit', ['service' => $websiteService, 'editData' => array_merge($websiteService->toArray(), $websiteService->draft_content ?? []), 'mediaAssets' => MediaAsset::query()->where('mime_type', 'like', 'image/%')->orderBy('original_name')->get()]);
    }

    public function update(Request $request, WebsiteService $websiteService, RichTextSanitizer $sanitizer, CmsAuditLogger $audit): RedirectResponse
    {
        CmsRevision::query()->create(['revisable_type' => $websiteService->getMorphClass(), 'revisable_id' => $websiteService->id, 'snapshot' => $websiteService->toArray(), 'user_id' => $request->user()->id]);
        $data = $this->validated($request, $sanitizer, $websiteService->id);
        $draft = Arr::only($data, ['number', 'title', 'slug', 'description', 'description_html', 'image_path', 'media_asset_id', 'image_alt', 'subservices', 'content', 'seo_title', 'seo_description']);
        $status = in_array($data['status'], ['draft', 'unpublished'], true) ? $data['status'] : $websiteService->status;
        $websiteService->update(['draft_content' => $draft, 'status' => $status, 'updated_by' => $request->user()->id]);
        $audit->record('service.draft_saved', 'Saved service draft: '.$websiteService->title, $websiteService);

        return back()->with('success', 'Service draft saved. Publish it when ready.');
    }

    public function publish(Request $request, WebsiteService $websiteService, CmsAuditLogger $audit): RedirectResponse
    {
        abort_unless(is_array($websiteService->draft_content), 422, 'There are no saved changes to publish.');
        CmsRevision::query()->create(['revisable_type' => $websiteService->getMorphClass(), 'revisable_id' => $websiteService->id, 'snapshot' => $websiteService->toArray(), 'user_id' => $request->user()->id]);
        $websiteService->update([...$websiteService->draft_content, 'draft_content' => null, 'status' => 'published', 'updated_by' => $request->user()->id]);
        $audit->record('service.published', 'Published service: '.$websiteService->title, $websiteService);

        return back()->with('success', 'Service changes are now live.');
    }

    public function destroy(WebsiteService $websiteService, CmsAuditLogger $audit): RedirectResponse
    {
        $title = $websiteService->title;
        $websiteService->delete();
        $audit->record('service.deleted', 'Deleted service: '.$title);

        return to_route('admin.services.index')->with('success', 'Service deleted.');
    }

    public function reorder(Request $request, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate(['services' => ['required', 'array'], 'services.*' => ['integer', 'distinct', 'exists:website_services,id']]);
        DB::transaction(function () use ($data): void {
            foreach ($data['services'] as $index => $id) {
                WebsiteService::query()->whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
            }
        });
        $audit->record('services.reordered', 'Reordered website services');

        return back()->with('success', 'Service order saved.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, RichTextSanitizer $sanitizer, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'number' => ['required', 'string', 'max:12'],
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'alpha_dash', 'max:160', 'unique:website_services,slug'.($ignoreId ? ','.$ignoreId : '')],
            'description' => ['required', 'string', 'max:320'],
            'description_html' => ['nullable', 'string', 'max:30000'],
            'image_path' => ['nullable', 'string', 'max:500', 'regex:/^(assets|storage)\/[A-Za-z0-9._\/-]+$/'],
            'media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'image_alt' => ['nullable', 'string', 'max:500'],
            'subservices_text' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'in:draft,published,unpublished'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'content_json' => ['nullable', 'string', 'max:12000'],
        ]);

        $content = json_decode((string) ($data['content_json'] ?? '{}'), true);
        abort_unless(is_array($content), 422, 'Service page content must contain valid JSON.');
        $content = array_merge(config('cms.service_page_defaults'), array_intersect_key($content, config('cms.service_page_defaults')));

        foreach ($content as $value) {
            abort_unless(is_string($value), 422, 'Service page content values must be text.');
        }

        return [
            ...$data,
            'description_html' => $sanitizer->clean($data['description_html'] ?? '<p>'.e($data['description']).'</p>'),
            'subservices' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['subservices_text'] ?? '') ?: []))),
            'content' => $content,
            'sort_order' => (int) ($data['number'] ?? 0),
        ];
    }
}
