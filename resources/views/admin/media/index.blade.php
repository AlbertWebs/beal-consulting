@extends('admin.layouts.app', ['title' => 'Media library'])
@section('content')
<div style="margin-bottom:20px"><p class="cms-muted" style="margin:0">MEDIA</p><h1 class="cms-title">Media library</h1><p class="cms-muted">Upload and reuse images. Built-in brand assets are protected from deletion.</p></div>
<div class="cms-card" style="margin-bottom:18px">
    <form method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" data-media-upload>
        @csrf
        <div class="cms-drop-zone" data-drop-zone>
            <div><strong>Drop images here</strong><span class="cms-muted"> or choose files from your device</span></div>
            <label class="cms-button" for="files">Choose images</label>
            <input class="cms-input" id="files" type="file" name="files[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple required>
            <small class="cms-muted">Up to 20 images, 10 MB each. Maximum dimensions 4000 × 4000.</small>
        </div>
        <div class="cms-grid cms-upload-previews" data-upload-preview aria-live="polite"></div>
        <div class="cms-field"><label class="cms-label">Alternative text (applies to all selected)</label><input class="cms-input" name="alt_text" maxlength="500"></div>
        <div data-upload-status aria-live="polite" class="cms-muted"></div>
        <div class="cms-actions" style="margin-top:10px"><button class="cms-button">Upload images</button><button class="cms-button" style="background:#7a8795" type="button" data-upload-cancel hidden>Cancel upload</button></div>
    </form>
</div>
<form method="get" style="margin:0 0 15px"><input class="cms-input" name="q" value="{{ request('q') }}" placeholder="Search filenames" aria-label="Search media"></form>
<div class="cms-grid">
    @foreach($assets as $asset)
        <article class="cms-card">
            <img class="cms-preview" style="width:100%;height:180px" src="{{ $asset->thumbnail_url }}" alt="{{ $asset->alt_text }}">
            <p style="margin:10px 0 2px"><strong>{{ $asset->original_name }}</strong></p>
            <p class="cms-muted" style="margin:0 0 9px">{{ number_format($asset->file_size/1024,1) }} KB · {{ $asset->width }}×{{ $asset->height }} · {{ $asset->disk==='assets'?'Built-in':'Uploaded' }}</p>
            <form method="post" action="{{ route('admin.media.update',$asset) }}">@csrf @method('PATCH')<label class="cms-label">Alternative text</label><input class="cms-input" name="alt_text" value="{{ $asset->alt_text }}"><button class="cms-button" style="margin-top:9px">Save alt text</button><a class="cms-button" style="margin-top:9px" href="{{ $asset->url }}" target="_blank" rel="noopener">Open</a><button class="cms-button" style="margin-top:9px" type="button" data-copy-url="{{ $asset->url }}">Copy URL</button></form>
            @unless($asset->is_system)<form method="post" action="{{ route('admin.media.destroy',$asset) }}" style="margin-top:8px" onsubmit="return confirm('Delete this image?')">@csrf @method('DELETE')<button class="cms-button" style="background:#9c3325">Delete</button></form>@endunless
        </article>
    @endforeach
</div>
<div style="margin-top:18px">{{ $assets->links() }}</div>
@endsection
@push('scripts')
<script>
    const mediaForm=document.querySelector('[data-media-upload]');
    const mediaInput=mediaForm?.querySelector('input[type=file]');
    const dropZone=mediaForm?.querySelector('[data-drop-zone]');
    const mediaPreview=mediaForm?.querySelector('[data-upload-preview]');
    const mediaStatus=mediaForm?.querySelector('[data-upload-status]');
    const cancelUpload=mediaForm?.querySelector('[data-upload-cancel]');
    let currentUpload=null;
    const showSelectedFiles=()=>{
        mediaPreview.replaceChildren();
        [...mediaInput.files].forEach(file=>{
            const card=document.createElement('div');card.className='cms-card';
            const image=document.createElement('img');image.className='cms-preview';image.style='width:100%;height:130px';image.src=URL.createObjectURL(file);image.alt='Preview of '+file.name;
            const label=document.createElement('p');label.textContent=`${file.name} · ${(file.size/1024/1024).toFixed(2)} MB`;
            const dimensions=document.createElement('small');dimensions.className='cms-muted';image.onload=()=>{dimensions.textContent=`${image.naturalWidth} × ${image.naturalHeight} px`;URL.revokeObjectURL(image.src)};
            card.append(image,label,dimensions);mediaPreview.append(card);
        });
    };
    mediaInput?.addEventListener('change',showSelectedFiles);
    dropZone?.addEventListener('dragover',event=>{event.preventDefault();dropZone.classList.add('is-dragging')});
    dropZone?.addEventListener('dragleave',()=>dropZone.classList.remove('is-dragging'));
    dropZone?.addEventListener('drop',event=>{event.preventDefault();dropZone.classList.remove('is-dragging');const transfer=new DataTransfer();[...event.dataTransfer.files].forEach(file=>transfer.items.add(file));mediaInput.files=transfer.files;mediaInput.dispatchEvent(new Event('change',{bubbles:true}))});
    cancelUpload?.addEventListener('click',()=>currentUpload?.abort());
    mediaForm?.addEventListener('submit',event=>{
        event.preventDefault();mediaStatus.textContent='Starting upload…';cancelUpload.hidden=false;
        currentUpload=new XMLHttpRequest();currentUpload.open('POST',mediaForm.action);currentUpload.setRequestHeader('X-CSRF-TOKEN',document.querySelector('meta[name=csrf-token]').content);
        currentUpload.upload.addEventListener('progress',progress=>{if(progress.lengthComputable)mediaStatus.textContent=`Uploading ${Math.round(progress.loaded/progress.total*100)}% (${(progress.loaded/1024/1024).toFixed(1)} of ${(progress.total/1024/1024).toFixed(1)} MB)`});
        currentUpload.addEventListener('load',()=>{cancelUpload.hidden=true;if(currentUpload.status>=200&&currentUpload.status<400){mediaStatus.textContent='Upload complete';window.location.reload()}else{mediaStatus.textContent='Upload failed. Check file type, dimensions and size, then retry.'}});
        currentUpload.addEventListener('error',()=>{cancelUpload.hidden=true;mediaStatus.textContent='Upload failed. Check your connection and retry.'});
        currentUpload.addEventListener('abort',()=>{cancelUpload.hidden=true;mediaStatus.textContent='Upload cancelled.'});
        currentUpload.send(new FormData(mediaForm));
    });
</script>
@endpush
