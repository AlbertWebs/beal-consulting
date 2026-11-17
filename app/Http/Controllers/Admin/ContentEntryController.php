<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsRevision;
use App\Models\ContentEntry;
use App\Models\MediaAsset;
use App\Services\CmsAuditLogger;
use App\Services\RichTextSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContentEntryController extends Controller
{
    public function index(string $type): View
    {
        $this->validateType($type);

        return view('admin.content.index', [
            'type' => $type,
            'label' => ContentEntry::TYPES[$type],
            'entries' => ContentEntry::query()->where('type', $type)->orderBy('sort_order')->orderByDesc('updated_at')->get(),
        ]);
    }

    public function create(string $type): View
    {
        $this->validateType($type);

        return $this->form($type, new ContentEntry(['type' => $type, 'status' => 'draft']));
    }

    public function store(Request $request, string $type, RichTextSanitizer $sanitizer, CmsAuditLogger $audit): RedirectResponse
    {
        $this->validateType($type);
        $data = $this->validated($request, $type, $sanitizer);
        $entry = ContentEntry::query()->create([
            ...Arr::except($data, ['_token']),
            'type' => $type,
            'status' => 'draft',
            'draft_data' => $data,
            'updated_by' => $request->user()->id,
        ]);
        $audit->record('content.created', 'Created '.$type.' draft: '.$entry->title, $entry);

        return to_route('admin.content.edit', [$type, $entry])->with('success', 'Draft created. Review and publish when ready.');
    }

    public function edit(string $type, ContentEntry $contentEntry): View
    {
        $this->validateEntry($type, $contentEntry);
        $entry = clone $contentEntry;
        if (is_array($entry->draft_data)) {
            $entry->forceFill($entry->draft_data);
        }

        return $this->form($type, $entry);
    }

    public function update(Request $request, string $type, ContentEntry $contentEntry, RichTextSanitizer $sanitizer, CmsAuditLogger $audit): RedirectResponse
    {
        $this->validateEntry($type, $contentEntry);
        $data = $this->validated($request, $type, $sanitizer, $contentEntry->id);
        CmsRevision::query()->create(['revisable_type' => $contentEntry->getMorphClass(), 'revisable_id' => $contentEntry->id, 'snapshot' => $contentEntry->toArray(), 'user_id' => $request->user()->id]);
        $contentEntry->update([
            'draft_data' => $data,
            'status' => $request->input('visibility') === 'unpublished' ? 'unpublished' : $contentEntry->status,
            'updated_by' => $request->user()->id,
        ]);
        $audit->record('content.draft_saved', 'Saved '.$type.' draft: '.$contentEntry->title, $contentEntry);

        return back()->with('success', 'Draft saved. Publish it when ready.');
    }

    public function publish(Request $request, string $type, ContentEntry $contentEntry, CmsAuditLogger $audit): RedirectResponse
    {
        $this->validateEntry($type, $contentEntry);
        abort_unless(is_array($contentEntry->draft_data), 422, 'There are no saved changes to publish.');
        DB::transaction(function () use ($contentEntry, $request): void {
            CmsRevision::query()->create(['revisable_type' => $contentEntry->getMorphClass(), 'revisable_id' => $contentEntry->id, 'snapshot' => $contentEntry->toArray(), 'user_id' => $request->user()->id]);
            $contentEntry->update([...$contentEntry->draft_data, 'draft_data' => null, 'status' => 'published', 'published_at' => $contentEntry->draft_data['published_at'] ?? $contentEntry->published_at ?? now(), 'updated_by' => $request->user()->id]);
        });
        $audit->record('content.published', 'Published '.$type.': '.$contentEntry->title, $contentEntry);

        return back()->with('success', 'Content is now published.');
    }

    public function reorder(Request $request, string $type, CmsAuditLogger $audit): RedirectResponse
    {
        $this->validateType($type);
        $data = $request->validate(['entries' => ['required', 'array'], 'entries.*' => ['required', 'integer', 'distinct', 'exists:content_entries,id']]);
        abort_unless(ContentEntry::query()->where('type', $type)->whereIn('id', $data['entries'])->count() === count($data['entries']), 422);
        DB::transaction(function () use ($data, $type): void {
            foreach ($data['entries'] as $index => $id) {
                ContentEntry::query()->where('type', $type)->whereKey($id)->update(['sort_order' => ($index + 1) * 10]);
            }
        });
        $audit->record('content.reordered', 'Reordered '.$type.' entries');

        return back()->with('success', 'Display order saved.');
    }

    private function form(string $type, ContentEntry $entry): View
    {
        return view('admin.content.edit', [
            'type' => $type,
            'label' => ContentEntry::TYPES[$type],
            'entry' => $entry,
            'mediaAssets' => MediaAsset::query()->where('mime_type', 'like', 'image/%')->orderBy('original_name')->get(),
        ]);
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, string $type, RichTextSanitizer $sanitizer, ?int $ignoreId = null): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'alpha_dash', 'max:180', Rule::unique('content_entries', 'slug')->where('type', $type)->ignore($ignoreId)],
            'summary' => ['nullable', 'string', 'max:600'],
            'content_html' => ['nullable', 'string', 'max:50000'],
            'category' => ['nullable', 'string', 'max:120'],
            'author' => ['nullable', 'string', 'max:160'],
            'location' => ['nullable', 'string', 'max:180'],
            'scale' => ['nullable', 'string', 'max:180'],
            'scope' => ['nullable', 'string', 'max:5000'],
            'outcomes' => ['nullable', 'string', 'max:5000'],
            'experience_source' => ['nullable', 'in:beal_engagement,previous_professional'],
            'media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
            'image_alt' => ['nullable', 'string', 'max:500'],
            'seo_title' => ['nullable', 'string', 'max:160'],
            'seo_description' => ['nullable', 'string', 'max:320'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'is_featured' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'visibility' => ['nullable', 'in:published,unpublished'],
        ];
        if ($type === 'project') {
            $rules['experience_source'] = ['required', 'in:beal_engagement,previous_professional'];
            $rules['gallery_media_ids'] = ['nullable', 'array', 'max:20'];
            $rules['gallery_media_ids.*'] = ['required', 'integer', 'distinct', 'exists:media_assets,id'];
        }

        $data = $request->validate($rules);
        $data['content_html'] = $sanitizer->clean($data['content_html'] ?? '');
        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_featured'] = $request->boolean('is_featured');
        if ($type === 'project') {
            $data['gallery_media_ids'] = array_values(array_map('intval', $data['gallery_media_ids'] ?? []));
        }
        unset($data['visibility']);

        return $data;
    }

    private function validateType(string $type): void
    {
        abort_unless(array_key_exists($type, ContentEntry::TYPES), 404);
    }

    private function validateEntry(string $type, ContentEntry $entry): void
    {
        $this->validateType($type);
        abort_unless($entry->type === $type, 404);
    }
}
