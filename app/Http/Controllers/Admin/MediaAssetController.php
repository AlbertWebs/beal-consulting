<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentEntry;
use App\Models\MediaAsset;
use App\Models\PageSection;
use App\Models\SiteSetting;
use App\Models\WebsiteService;
use App\Services\CmsAuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MediaAssetController extends Controller
{
    public function index(Request $request): View
    {
        $assets = MediaAsset::query()->where('mime_type', 'like', 'image/%')->when($request->string('q')->isNotEmpty(), fn ($query) => $query->where('original_name', 'like', '%'.$request->string('q').'%'))->latest()->paginate(36)->withQueryString();

        return view('admin.media.index', ['assets' => $assets]);
    }

    public function store(Request $request, CmsAuditLogger $audit): RedirectResponse
    {
        $data = $request->validate(['files' => ['required', 'array', 'max:20'], 'files.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240', 'dimensions:max_width=4000,max_height=4000'], 'alt_text' => ['nullable', 'string', 'max:500']]);
        $diskName = (string) config('filesystems.cms_media_disk', 'public');

        foreach ($data['files'] as $file) {
            $name = Str::uuid().'.'.$file->extension();
            $path = $file->storeAs('cms-media', $name, $diskName);
            $dimensions = @getimagesize($file->getRealPath()) ?: [null, null];
            $asset = MediaAsset::query()->create([
                'disk' => $diskName, 'path' => $path, 'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(), 'file_size' => $file->getSize(), 'width' => $dimensions[0],
                'height' => $dimensions[1], 'alt_text' => $data['alt_text'] ?? '', 'uploaded_by' => $request->user()->id,
            ]);
            $thumbnail = $this->makeThumbnail($file->getRealPath(), $asset->id, $diskName);

            if ($thumbnail !== null) {
                $asset->update(['thumbnail_path' => $thumbnail]);
            }
        }

        $audit->record('media.uploaded', 'Uploaded '.$request->file('files')[0]->getClientOriginalName());

        return back()->with('success', 'Media uploaded successfully.');
    }

    public function update(Request $request, MediaAsset $mediaAsset, CmsAuditLogger $audit): RedirectResponse
    {
        $mediaAsset->update($request->validate(['alt_text' => ['nullable', 'string', 'max:500']]));
        $audit->record('media.updated', 'Updated media metadata', $mediaAsset);

        return back()->with('success', 'Media details saved.');
    }

    public function destroy(MediaAsset $mediaAsset, CmsAuditLogger $audit): RedirectResponse
    {
        abort_if($mediaAsset->is_system, 422, 'Built-in brand and website imagery cannot be deleted.');
        $isInDraft = PageSection::query()->where('draft_media_asset_id', $mediaAsset->id)->exists()
            || WebsiteService::query()->whereJsonContains('draft_content->media_asset_id', $mediaAsset->id)->exists()
            || ContentEntry::query()->whereJsonContains('draft_data->media_asset_id', $mediaAsset->id)->exists();
        $isEmbedded = PageSection::query()->get(['content', 'draft_content'])->contains(fn (PageSection $section): bool => $this->containsMediaUrl([$section->content, $section->draft_content], $mediaAsset->url))
            || WebsiteService::query()->get(['description_html', 'content', 'draft_content'])->contains(fn (WebsiteService $service): bool => $this->containsMediaUrl([$service->description_html, $service->content, $service->draft_content], $mediaAsset->url))
            || ContentEntry::query()->get(['content_html', 'draft_data'])->contains(fn (ContentEntry $entry): bool => $this->containsMediaUrl([$entry->content_html, $entry->draft_data], $mediaAsset->url))
            || SiteSetting::query()->get(['value'])->contains(fn (SiteSetting $setting): bool => $this->containsMediaUrl($setting->value, $mediaAsset->url));
        abort_if($isInDraft || $isEmbedded || $mediaAsset->sections()->exists() || $mediaAsset->services()->exists() || $mediaAsset->contentEntries()->exists() || $mediaAsset->settings()->exists() || $mediaAsset->pages()->exists(), 422, 'This image is currently in use. Replace its references before deleting it.');
        Storage::disk($mediaAsset->disk)->delete(array_filter([$mediaAsset->path, $mediaAsset->thumbnail_path]));
        $mediaAsset->delete();
        $audit->record('media.deleted', 'Deleted media asset');

        return back()->with('success', 'Media deleted.');
    }

    private function makeThumbnail(string $source, int $id, string $diskName): ?string
    {
        if (! function_exists('imagecreatefromstring')) {
            return null;
        }

        $image = @imagecreatefromstring((string) file_get_contents($source));

        if ($image === false) {
            return null;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, 480 / max($width, $height));
        $thumbnail = imagecreatetruecolor(max(1, (int) ($width * $scale)), max(1, (int) ($height * $scale)));
        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, imagesx($thumbnail), imagesy($thumbnail), $width, $height);
        $format = function_exists('imagewebp') ? 'webp' : 'jpg';
        $path = 'cms-media/thumbnails/'.$id.'.'.$format;
        $temporaryFile = tempnam(sys_get_temp_dir(), 'beal-thumb-');

        if ($format === 'webp') {
            imagewebp($thumbnail, $temporaryFile, 82);
        } else {
            $background = imagecreatetruecolor(imagesx($thumbnail), imagesy($thumbnail));
            $white = imagecolorallocate($background, 255, 255, 255);
            imagefill($background, 0, 0, $white);
            imagecopy($background, $thumbnail, 0, 0, 0, 0, imagesx($thumbnail), imagesy($thumbnail));
            imagejpeg($background, $temporaryFile, 84);
            imagedestroy($background);
        }
        Storage::disk($diskName)->put($path, file_get_contents($temporaryFile));
        unlink($temporaryFile);
        imagedestroy($thumbnail);
        imagedestroy($image);

        return $path;
    }

    private function containsMediaUrl(mixed $value, string $url): bool
    {
        if (is_string($value)) {
            return str_contains($value, $url);
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $child) {
            if ($this->containsMediaUrl($child, $url)) {
                return true;
            }
        }

        return false;
    }
}
