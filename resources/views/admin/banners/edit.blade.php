@extends('admin.layouts.app', ['title' => 'Website banners'])

@section('content')
<div class="cms-banner-heading">
    <p class="cms-muted" style="margin:0">SITE IMAGES</p>
    <h1 class="cms-title">Website banners</h1>
    <p class="cms-muted">Update homepage imagery, page headers, service images and article banners from one place. Choose an existing image, preview it, then save your changes once.</p>
</div>

<div class="cms-card cms-banner-upload-card">
    <div>
        <h2 style="margin:0 0 5px;font-size:18px">Add a new banner image</h2>
        <p class="cms-muted" style="margin:0">Upload once and use the new image on a banner below. JPG, PNG, WebP or GIF, up to 10 MB and 4000 × 4000 px.</p>
    </div>
    <form action="{{ route('admin.media.store') }}" method="post" enctype="multipart/form-data" data-banner-upload>
        @csrf
        <label class="cms-label" for="banner-upload-target">Use the uploaded image for</label>
        <select class="cms-select" id="banner-upload-target" data-banner-upload-target>
            @foreach($groups as $group => $groupSlots)
                <optgroup label="{{ $group }}">
                    @foreach($groupSlots as $key => $slot)
                        <option value="{{ $key }}">{{ $slot['label'] }}</option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>
        <label class="cms-label" for="banner-upload-files">Choose image</label>
        <input class="cms-input" id="banner-upload-files" type="file" name="files[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
        <label class="cms-label" for="banner-upload-alt">Alternative text <span class="cms-muted">(optional)</span></label>
        <input class="cms-input" id="banner-upload-alt" type="text" name="alt_text" maxlength="500" placeholder="Describe the image briefly">
        <div class="cms-actions"><button class="cms-button" type="submit" data-banner-upload-submit>Upload image</button><span class="cms-muted" data-banner-upload-status role="status" aria-live="polite"></span></div>
    </form>
</div>

<nav class="cms-actions cms-banner-jump-links" aria-label="Banner groups">
    @foreach($groups as $group => $groupSlots)
        <a class="cms-button" href="#banner-group-{{ \Illuminate\Support\Str::slug($group) }}">{{ $group }} <span class="cms-banner-count">{{ $groupSlots->count() }}</span></a>
    @endforeach
</nav>

<form method="post" action="{{ route('admin.banners.update') }}" data-banner-settings-form>
    @csrf
    @method('PUT')
    @foreach($groups as $group => $groupSlots)
        <section class="cms-banner-group" id="banner-group-{{ \Illuminate\Support\Str::slug($group) }}">
            <div class="cms-banner-group-heading"><div><h2>{{ $group }}</h2><p class="cms-muted">Select an image for each location. The preview updates as you make your selections.</p></div><span class="cms-banner-count">{{ $groupSlots->count() }} {{ \Illuminate\Support\Str::plural('banner', $groupSlots->count()) }}</span></div>
            <div class="cms-grid cms-banner-grid">
                @foreach($groupSlots as $key => $slot)
                    @php($assignment = $assignments->get('banners.'.$key))
                    @php($currentPreview = $assignment?->media?->thumbnail_url ?? $slot['default_preview'])
                    <article class="cms-card cms-banner-card">
                        <img class="cms-preview cms-banner-preview" src="{{ $currentPreview }}" alt="Preview of {{ $slot['label'] }} banner" data-banner-preview>
                        <div class="cms-banner-card-body">
                            <div><h3>{{ $slot['label'] }}</h3><p class="cms-muted">{{ $slot['description'] }}</p></div>
                            <label class="cms-label" for="banner-{{ $key }}">Image</label>
                            <select class="cms-select" id="banner-{{ $key }}" name="banners[{{ $key }}]" data-banner-select data-default-preview="{{ $slot['default_preview'] }}">
                                <option value="" @selected(! $assignment?->media_asset_id)>Use current site image</option>
                                @foreach($assets as $asset)
                                    <option value="{{ $asset->id }}" data-preview="{{ $asset->thumbnail_url }}" @selected($assignment?->media_asset_id === $asset->id)>{{ $asset->original_name }} · {{ $asset->width }} × {{ $asset->height }}</option>
                                @endforeach
                            </select>
                            <small class="cms-muted">Your selection appears on the website as soon as you save.</small>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endforeach
    <div class="cms-banner-save-bar"><span class="cms-muted">Save once to apply all banner image changes.</span><button class="cms-button" type="submit">Save all banners</button></div>
</form>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('[data-banner-select]').forEach((select) => {
        select.addEventListener('change', () => {
            const option = select.selectedOptions[0];
            const preview = select.closest('.cms-banner-card')?.querySelector('[data-banner-preview]');
            if (preview) {
                preview.src = option.dataset.preview || select.dataset.defaultPreview;
            }
        });
    });

    const bannerUpload = document.querySelector('[data-banner-upload]');
    const bannerUploadStatus = bannerUpload?.querySelector('[data-banner-upload-status]');
    const bannerUploadButton = bannerUpload?.querySelector('[data-banner-upload-submit]');

    bannerUpload?.addEventListener('submit', async (event) => {
        event.preventDefault();
        bannerUploadButton.disabled = true;
        bannerUploadStatus.textContent = 'Uploading image…';

        try {
            const response = await fetch(bannerUpload.action, {
                method: 'POST',
                body: new FormData(bannerUpload),
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                },
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(Object.values(result.errors || {}).flat()[0] || 'Upload failed. Check the image type and size, then try again.');
            }

            const targetKey = bannerUpload.querySelector('[data-banner-upload-target]').value;
            const target = document.querySelector(`#banner-${CSS.escape(targetKey)}`);
            result.assets.forEach((asset) => {
                document.querySelectorAll('[data-banner-select]').forEach((select) => {
                    if (!select.querySelector(`option[value="${asset.id}"]`)) {
                        const option = new Option(`${asset.name} · new image`, asset.id);
                        option.dataset.preview = asset.thumbnail_url;
                        select.add(option);
                    }
                });
            });

            const firstAsset = result.assets[0];
            if (target && firstAsset) {
                target.value = String(firstAsset.id);
                target.dispatchEvent(new Event('change'));
            }
            bannerUploadStatus.textContent = 'Image uploaded and selected. Save all banners below to apply it.';
            bannerUpload.reset();
        } catch (error) {
            bannerUploadStatus.textContent = error.message || 'Upload failed. Please try again.';
        } finally {
            bannerUploadButton.disabled = false;
        }
    });
</script>
@endpush
