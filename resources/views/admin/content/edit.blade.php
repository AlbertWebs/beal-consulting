@extends('admin.layouts.app', ['title' => ($entry->exists ? 'Edit ' : 'Add ').$label])
@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;gap:20px;margin-bottom:22px"><div><a class="cms-muted" href="{{ route('admin.content.index', ['type' => $type]) }}">← {{ $label }}</a><h1 class="cms-title" style="margin-top:8px">{{ $entry->title ?: 'New '.$label.' entry' }}</h1><p class="cms-muted">{{ ucfirst($entry->status ?: 'draft') }} · {{ $entry->draft_data ? 'Draft changes are saved' : 'Edits are held as a draft until published' }}</p></div>@if($entry->exists && $entry->status==='published')<a class="cms-button" href="{{ route($type==='sector'?'sectors.show':($type==='insight'?'insights.show':'experience.show'), $entry->slug) }}" target="_blank" rel="noopener">View published ↗</a>@endif</div>
<form method="post" action="{{ $entry->exists ? route('admin.content.update', [$type, $entry]) : route('admin.content.store', ['type' => $type]) }}">@csrf @if($entry->exists)@method('PUT')@endif
<div class="cms-card" style="margin-bottom:18px"><div class="cms-grid" style="grid-template-columns:repeat(2,minmax(0,1fr))"><div class="cms-field"><label class="cms-label" for="title">Title</label><input class="cms-input" id="title" name="title" value="{{ old('title',$entry->title) }}" required maxlength="180"></div><div class="cms-field"><label class="cms-label" for="slug">URL slug</label><input class="cms-input" id="slug" name="slug" value="{{ old('slug',$entry->slug) }}" required maxlength="180"></div><div class="cms-field"><label class="cms-label" for="summary">Summary</label><textarea class="cms-input" id="summary" name="summary" rows="3" maxlength="600">{{ old('summary',$entry->summary) }}</textarea></div><div class="cms-field"><label class="cms-label" for="category">{{ $type==='insight'?'Category':'Sector' }}</label><input class="cms-input" id="category" name="category" value="{{ old('category',$entry->category) }}" maxlength="120"></div>
@if($type==='insight')<div class="cms-field"><label class="cms-label" for="author">Author attribution</label><input class="cms-input" id="author" name="author" value="{{ old('author',$entry->author) }}" maxlength="160"></div>@endif
@if($type==='project')<div class="cms-field"><label class="cms-label" for="experience_source">Experience attribution</label><select class="cms-select" id="experience_source" name="experience_source" required><option value="">Choose attribution</option><option value="beal_engagement" @selected(old('experience_source',$entry->experience_source)==='beal_engagement')>BEAL corporate engagement</option><option value="previous_professional" @selected(old('experience_source',$entry->experience_source)==='previous_professional')>Previous professional experience</option></select></div><div class="cms-field"><label class="cms-label" for="location">Location</label><input class="cms-input" id="location" name="location" value="{{ old('location',$entry->location) }}" maxlength="180"></div><div class="cms-field"><label class="cms-label" for="scope">Scope of involvement</label><textarea class="cms-input" id="scope" name="scope" rows="3">{{ old('scope',$entry->scope) }}</textarea></div><div class="cms-field"><label class="cms-label" for="outcomes">Key outcomes (verified only)</label><textarea class="cms-input" id="outcomes" name="outcomes" rows="3">{{ old('outcomes',$entry->outcomes) }}</textarea></div>@endif
@if($type==='project')<div class="cms-field"><label class="cms-label" for="scale">Scale (verified only)</label><input class="cms-input" id="scale" name="scale" value="{{ old('scale',$entry->scale) }}" maxlength="180"></div>@endif<div class="cms-field"><label class="cms-label" for="content_html">{{ $type==='sector'?'Sector capabilities and context':'Full content' }}</label><textarea class="cms-input" id="content_html" name="content_html" rows="12">{{ old('content_html',$entry->content_html) }}</textarea><small class="cms-muted">HTML is sanitized before saving. Do not publish unsupported client or project claims.</small></div>
<div class="cms-field"><label class="cms-label" for="media_asset_id">Featured image</label><select class="cms-select" id="media_asset_id" name="media_asset_id"><option value="">No featured image</option>@foreach($mediaAssets as $asset)<option value="{{ $asset->id }}" @selected((string)old('media_asset_id',$entry->media_asset_id)===(string)$asset->id)>{{ $asset->original_name }} - {{ $asset->width }}×{{ $asset->height }}</option>@endforeach</select></div><div class="cms-field"><label class="cms-label" for="image_alt">Image alternative text</label><input class="cms-input" id="image_alt" name="image_alt" value="{{ old('image_alt',$entry->image_alt) }}" maxlength="500"></div><div class="cms-field"><label class="cms-label" for="sort_order">Display order</label><input class="cms-input" id="sort_order" type="number" min="0" name="sort_order" value="{{ old('sort_order',$entry->sort_order ?? 0) }}"></div>@if($type==='insight')<div class="cms-field"><label class="cms-label" for="published_at">Publication date and time</label><input class="cms-input" id="published_at" type="datetime-local" name="published_at" value="{{ old('published_at',$entry->published_at?->format('Y-m-d\TH:i')) }}"></div><div class="cms-field"><label><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured',$entry->is_featured))> Feature this insight</label></div>@endif<div class="cms-field"><label class="cms-label" for="seo_title">SEO title</label><input class="cms-input" id="seo_title" name="seo_title" value="{{ old('seo_title',$entry->seo_title) }}" maxlength="160"></div><div class="cms-field"><label class="cms-label" for="seo_description">SEO description</label><textarea class="cms-input" id="seo_description" name="seo_description" rows="3" maxlength="320">{{ old('seo_description',$entry->seo_description) }}</textarea></div>@if($entry->exists)<div class="cms-field"><label class="cms-label" for="visibility">Publication</label><select class="cms-select" id="visibility" name="visibility"><option value="published">Keep published (save new draft)</option><option value="unpublished">Unpublish now</option></select></div>@endif
</div>
@if($type==='project')
    @php($galleryMediaIds = old('gallery_media_ids', $entry->gallery_media_ids ?? []))
    @php($galleryStageValues = old('gallery_stages', $entry->gallery_stages ?? []))
    @php($galleryStageOptions = config('cms.project_gallery_stages', []))
    <div class="cms-field cms-project-gallery" data-project-gallery data-upload-url="{{ route('admin.media.store') }}" data-stage-options="{{ json_encode($galleryStageOptions) }}">
        <label class="cms-label">Project gallery</label>
        <p class="cms-muted">Add up to 20 images and videos combined. Add several items to the same stage for a fuller story from plans and visualisations through construction to completion.</p>
        <div class="cms-drop-zone" data-gallery-drop-zone>
            <div><strong>Drop project images or videos here</strong><span class="cms-muted"> or choose files from your device</span></div>
            <label class="cms-button" for="gallery-files">Choose media</label>
            <input class="cms-input" id="gallery-files" type="file" accept="image/jpeg,image/png,image/webp,image/gif,video/mp4,video/webm" multiple data-gallery-file-input>
            <small class="cms-muted">Images: up to 10 MB and 4000 × 4000 pixels. Videos: MP4 or WebM up to 50 MB. Up to 20 gallery items.</small>
        </div>
        <div class="cms-grid cms-upload-previews" data-gallery-upload-preview aria-live="polite"></div>
        <div class="cms-gallery-grid" data-gallery-items>
            @foreach($galleryMediaIds as $galleryMediaId)
                @php($galleryAsset = $galleryAssets->firstWhere('id', (int) $galleryMediaId))
                @if($galleryAsset)
                    <article class="cms-gallery-item" data-gallery-item>
                        @if(str_starts_with($galleryAsset->mime_type, 'video/'))
                            <div class="cms-gallery-video-preview" aria-label="Video file"><span aria-hidden="true">▶</span><b>VIDEO</b></div>
                        @else
                            <img class="cms-preview" src="{{ $galleryAsset->thumbnail_url }}" alt="{{ $galleryAsset->alt_text ?: 'Project image for '.$entry->title }}">
                        @endif
                        <p>{{ $galleryAsset->original_name }}</p>
                        <input type="hidden" name="gallery_media_ids[]" value="{{ $galleryAsset->id }}">
                        <label class="cms-label" for="gallery-stage-{{ $galleryAsset->id }}">Project stage</label>
                        <select class="cms-select cms-gallery-stage-select" id="gallery-stage-{{ $galleryAsset->id }}" name="gallery_stages[{{ $galleryAsset->id }}]">
                            <option value="">Choose a stage</option>
                            @foreach($galleryStageOptions as $stageKey => $stageLabel)
                                <option value="{{ $stageKey }}" @selected(($galleryStageValues[$galleryAsset->id] ?? '') === $stageKey)>{{ $stageLabel }}</option>
                            @endforeach
                        </select>
                        <button class="cms-gallery-remove" type="button" data-gallery-remove>Remove item</button>
                    </article>
                @endif
            @endforeach
        </div>
        <div class="cms-actions">
            <button class="cms-button" type="button" data-gallery-upload disabled>Upload selected media</button>
            <span class="cms-muted" data-gallery-status role="status" aria-live="polite"></span>
        </div>
    </div>
@endif
<div class="cms-actions" style="margin-top:18px"><button class="cms-button cms-button-gold">Save draft</button>@if($entry->exists)<a class="cms-button" href="{{ route('admin.content.index',['type'=>$type]) }}">Back to list</a>@endif</div></div></form>
@if($entry->exists && $entry->draft_data)<form method="post" action="{{ route('admin.content.publish',[$type,$entry]) }}" onsubmit="return confirm('Publish this content for everyone?')">@csrf<button class="cms-button cms-button-gold">Publish changes</button></form>@endif
@endsection
@if($type==='project')
    @push('scripts')
        <script>
            (() => {
                const gallery = document.querySelector('[data-project-gallery]');
                if (!gallery) return;

                const fileInput = gallery.querySelector('[data-gallery-file-input]');
                const dropZone = gallery.querySelector('[data-gallery-drop-zone]');
                const uploadPreview = gallery.querySelector('[data-gallery-upload-preview]');
                const items = gallery.querySelector('[data-gallery-items]');
                const uploadButton = gallery.querySelector('[data-gallery-upload]');
                const status = gallery.querySelector('[data-gallery-status]');
                const stageOptions = JSON.parse(gallery.dataset.stageOptions || '{}');
                let selectedFiles = [];
                let previewObjectUrls = [];

                const bindRemoveButton = (button) => {
                    button.addEventListener('click', () => {
                        button.closest('[data-gallery-item]')?.remove();
                    });
                };

                gallery.querySelectorAll('[data-gallery-remove]').forEach(bindRemoveButton);

                const showSelectedFiles = () => {
                    previewObjectUrls.forEach((previewUrl) => URL.revokeObjectURL(previewUrl));
                    previewObjectUrls = [];
                    uploadPreview.replaceChildren();
                    selectedFiles.forEach((file) => {
                        const card = document.createElement('article');
                        card.className = 'cms-gallery-item';
                        const previewUrl = URL.createObjectURL(file);
                        previewObjectUrls.push(previewUrl);
                        let preview;
                        if (file.type.startsWith('video/')) {
                            preview = document.createElement('video');
                            preview.className = 'cms-preview cms-preview-video';
                            preview.src = previewUrl;
                            preview.controls = true;
                            preview.muted = true;
                            preview.preload = 'metadata';
                        } else {
                            preview = document.createElement('img');
                            preview.className = 'cms-preview';
                            preview.src = previewUrl;
                            preview.alt = `Preview of ${file.name}`;
                        }
                        const name = document.createElement('p');
                        name.textContent = `${file.name} · ${(file.size / 1024 / 1024).toFixed(2)} MB`;
                        card.append(preview, name);
                        uploadPreview.append(card);
                    });
                    uploadButton.disabled = selectedFiles.length === 0;
                };

                fileInput.addEventListener('change', () => {
                    selectedFiles = Array.from(fileInput.files || []);
                    showSelectedFiles();
                    status.textContent = selectedFiles.length ? `${selectedFiles.length} item(s) ready to upload.` : '';
                });

                dropZone.addEventListener('dragover', (event) => {
                    event.preventDefault();
                    dropZone.classList.add('is-dragging');
                });
                dropZone.addEventListener('dragleave', () => dropZone.classList.remove('is-dragging'));
                dropZone.addEventListener('drop', (event) => {
                    event.preventDefault();
                    dropZone.classList.remove('is-dragging');
                    const transfer = new DataTransfer();
                    Array.from(event.dataTransfer?.files || []).forEach((file) => transfer.items.add(file));
                    fileInput.files = transfer.files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                });

                const addGalleryAsset = (asset) => {
                    const card = document.createElement('article');
                    card.className = 'cms-gallery-item';
                    card.dataset.galleryItem = '';
                    let preview;
                    if (asset.mime_type?.startsWith('video/')) {
                        preview = document.createElement('div');
                        preview.className = 'cms-gallery-video-preview';
                        const playIcon = document.createElement('span');
                        playIcon.setAttribute('aria-hidden', 'true');
                        playIcon.textContent = '▶';
                        const videoLabel = document.createElement('b');
                        videoLabel.textContent = 'VIDEO';
                        preview.append(playIcon, videoLabel);
                    } else {
                        preview = document.createElement('img');
                        preview.className = 'cms-preview';
                        preview.src = asset.thumbnail_url;
                        preview.alt = asset.alt_text || `Project image for ${document.querySelector('#title').value}`;
                    }
                    const name = document.createElement('p');
                    name.textContent = asset.name;
                    const id = document.createElement('input');
                    id.type = 'hidden';
                    id.name = 'gallery_media_ids[]';
                    id.value = asset.id;
                    const stageLabel = document.createElement('label');
                    stageLabel.className = 'cms-label';
                    stageLabel.htmlFor = `gallery-stage-${asset.id}`;
                    stageLabel.textContent = 'Project stage';
                    const stageSelect = document.createElement('select');
                    stageSelect.className = 'cms-select cms-gallery-stage-select';
                    stageSelect.id = `gallery-stage-${asset.id}`;
                    stageSelect.name = `gallery_stages[${asset.id}]`;
                    const blankStage = document.createElement('option');
                    blankStage.value = '';
                    blankStage.textContent = 'Choose a stage';
                    stageSelect.append(blankStage);
                    Object.entries(stageOptions).forEach(([value, label]) => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = label;
                        stageSelect.append(option);
                    });
                    const remove = document.createElement('button');
                    remove.className = 'cms-gallery-remove';
                    remove.type = 'button';
                    remove.textContent = 'Remove item';
                    remove.dataset.galleryRemove = '';
                    bindRemoveButton(remove);
                    card.append(preview, name, id, stageLabel, stageSelect, remove);
                    items.append(card);
                };

                uploadButton.addEventListener('click', async () => {
                    const remaining = 20 - items.querySelectorAll('[data-gallery-item]').length;
                    if (selectedFiles.length > remaining) {
                        status.textContent = `This gallery can contain up to 20 items. Remove ${selectedFiles.length - remaining} item(s) first.`;
                        return;
                    }

                    uploadButton.disabled = true;
                    status.textContent = 'Uploading media…';
                    const formData = new FormData();
                    selectedFiles.forEach((file) => formData.append('files[]', file, file.name));

                    try {
                        const response = await fetch(gallery.dataset.uploadUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            },
                            body: formData,
                        });
                        const result = await response.json();
                        if (!response.ok) throw new Error(result.message || 'The images could not be uploaded.');
                        result.assets.forEach(addGalleryAsset);
                        const uploadedCount = result.assets.length;
                        selectedFiles = [];
                        fileInput.value = '';
                        showSelectedFiles();
                        status.textContent = `${uploadedCount} item(s) uploaded. Assign a stage, then save the draft to add them to this project.`;
                    } catch (error) {
                        uploadButton.disabled = false;
                        status.textContent = error instanceof Error ? error.message : 'The images could not be uploaded. Check the files and try again.';
                    }
                });
            })();
        </script>
    @endpush
@endif
