<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use App\Models\MediaAsset;
use App\Models\SiteSetting;
use App\Models\WebsitePage;
use App\Models\WebsiteService;
use App\Services\CmsAuditLogger;
use App\Services\WebsiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function edit(): View
    {
        $slots = config('cms.banners', []);
        $pages = WebsitePage::query()->with('sections.media')->whereIn('page_key', ['home', 'about', 'services', 'sectors', 'insights', 'experience', 'contact'])->get()->keyBy('page_key');

        foreach ($slots as $key => $slot) {
            $pageSection = isset($slot['page'], $slot['section'])
                ? $pages->get($slot['page'])?->sections->firstWhere('section_key', $slot['section'])
                : null;
            $slot['default_preview'] = $pageSection?->media?->thumbnail_url ?? asset($slot['image']);
            $slots[$key] = $slot;
        }

        foreach (WebsiteService::query()->with('media')->orderBy('sort_order')->get() as $service) {
            $imagePath = ltrim((string) $service->image_path, '/');
            $slots['service_'.$service->id] = [
                'label' => $service->title,
                'description' => 'Service card and service detail image. Changes apply wherever this service image is used.',
                'group' => 'Service images',
                'default_preview' => $service->media?->thumbnail_url ?? asset($imagePath),
            ];
        }

        $entryFallbacks = [
            'sector' => 'assets/home-image/urban-infrastructure.jpg',
            'insight' => 'assets/home-image/nairobi-skyline.jpg',
            'project' => 'assets/home-image/city-development.jpg',
        ];

        foreach (ContentEntry::query()->with('media')->orderBy('type')->orderBy('sort_order')->orderBy('title')->get() as $entry) {
            $slots['entry_'.$entry->id] = [
                'label' => $entry->title,
                'description' => 'Banner for this '.match ($entry->type) {
                    'sector' => 'sector profile',
                    'insight' => 'insight article',
                    default => 'project experience page',
                }.'.',
                'group' => match ($entry->type) {
                    'sector' => 'Sector profiles',
                    'insight' => 'Insight articles',
                    default => 'Project experience pages',
                },
                'default_preview' => $entry->media?->thumbnail_url ?? asset($entryFallbacks[$entry->type] ?? 'assets/home-image/city-development.jpg'),
            ];
        }

        $assignments = SiteSetting::query()
            ->with('media')
            ->whereIn('setting_key', collect(array_keys($slots))->map(fn (string $key): string => 'banners.'.$key))
            ->get()
            ->keyBy('setting_key');
        $assets = MediaAsset::query()->where('mime_type', 'like', 'image/%')->orderByDesc('created_at')->get();

        return view('admin.banners.edit', [
            'slots' => $slots,
            'groups' => collect($slots)->groupBy('group'),
            'assignments' => $assignments,
            'assets' => $assets,
        ]);
    }

    public function update(Request $request, WebsiteSettings $settings, CmsAuditLogger $audit): RedirectResponse
    {
        $slots = $this->currentSlotKeys();
        $rules = [];

        foreach ($slots as $key) {
            $rules['banners.'.$key] = ['nullable', 'integer', 'exists:media_assets,id'];
        }

        $data = $request->validate($rules)['banners'] ?? [];
        $selectedIds = collect($data)->filter(fn (mixed $id): bool => filled($id))->map(fn (mixed $id): int => (int) $id)->unique()->values();
        $assets = MediaAsset::query()->whereIn('id', $selectedIds)->get()->keyBy('id');

        abort_unless($assets->count() === $selectedIds->count() && $assets->every(fn (MediaAsset $asset): bool => str_starts_with($asset->mime_type, 'image/')), 422, 'Choose an image from the media library for each banner.');

        DB::transaction(function () use ($slots, $data, $request): void {
            foreach ($slots as $key) {
                $assetId = filled($data[$key] ?? null) ? (int) $data[$key] : null;

                SiteSetting::query()->updateOrCreate(
                    ['setting_key' => 'banners.'.$key],
                    [
                        'setting_group' => 'banners',
                        'value' => ['value' => ''],
                        'media_asset_id' => $assetId,
                        'updated_by' => $request->user()->id,
                    ],
                );
            }
        });

        $settings->clearCache();
        $audit->record('banners.updated', 'Updated website banners');

        return to_route('admin.banners.edit')->with('success', 'Banner selections saved. Your website images are now up to date.');
    }

    /** @return list<string> */
    private function currentSlotKeys(): array
    {
        $keys = array_keys(config('cms.banners', []));
        $keys = [...$keys, ...WebsiteService::query()->pluck('id')->map(fn (int $id): string => 'service_'.$id)->all()];
        $keys = [...$keys, ...ContentEntry::query()->pluck('id')->map(fn (int $id): string => 'entry_'.$id)->all()];

        return $keys;
    }
}
