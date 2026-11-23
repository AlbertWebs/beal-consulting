@extends('layouts.public', ['ogType' => $type === 'insight' ? 'article' : 'website'])
@section('content')
@php($parentLabel = $type==='sector'?'Sectors':($type==='insight'?'Insights & Intelligence':'Projects & Experience'))
@php($parentUrl = route('pages.'.($type==='sector'?'sectors':($type==='insight'?'insights':'experience'))))
<section class="secondary-hero secondary-entry-hero{{ $type === 'project' ? ' secondary-project-detail-hero' : '' }}" style="--secondary-image:url('{{ $bannerImage }}')"><div class="secondary-hero-shade"></div><div class="secondary-hero-inner"><x-breadcrumb :items="[['label'=>$parentLabel,'url'=>$parentUrl],['label'=>$entry->title]]"/><p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>{{ $type==='sector'?'SECTOR PERSPECTIVE':($type==='insight'?'INSIGHTS & INTELLIGENCE':($entry->experience_source==='beal_engagement'?'BEAL ENGAGEMENT':'OUR TEAM’S PRIOR EXPERIENCE')) }}</p><h1>{{ $entry->title }}</h1>@if($entry->summary && !($type === 'project' && \Illuminate\Support\Str::startsWith($entry->summary, 'Project experience gained by')))<p class="secondary-hero-subtitle">{{ $entry->summary }}</p>@endif</div><span class="secondary-hero-index">BEAL / {{ $type==='sector'?'SECTOR':($type==='insight'?'PERSPECTIVE':'EXPERIENCE') }}</span></section>
@php($projectContent = (string) ($entry->content_html ?? ''))
@if($type === 'project' && \Illuminate\Support\Str::startsWith(trim(strip_tags($projectContent)), 'This project is included as part of the Founding Director'))
    @php($projectContent = '')
@endif
<article @class(['secondary-article', 'section-pad', 'secondary-sector-article' => $type === 'sector', 'secondary-project-article' => $type === 'project'])>
    @if($type === 'project')
        <div class="project-detail-layout">
            <div class="project-detail-main">
                <div class="secondary-index"><span>PROJECT RECORD</span><i></i><span>{{ $entry->experience_source === 'beal_engagement' ? 'BEAL ENGAGEMENT' : 'EARLIER TEAM EXPERIENCE' }}</span></div>
                <div class="project-detail-attribution"><span class="project-detail-attribution-icon" aria-hidden="true">i</span><p>{{ $entry->experience_source === 'beal_engagement' ? 'This project was delivered through a BEAL Consulting engagement.' : 'This project reflects experience our team gained through earlier professional roles. It was delivered through those engagements, before BEAL Consulting.' }}</p></div>
                @if($projectContent)<div class="secondary-rich-content project-detail-copy">{!! $projectContent !!}</div>@endif
                @if($entry->scope)<section class="project-detail-section"><span>01 / INVOLVEMENT</span><h2>Scope of involvement</h2><p>{{ $entry->scope }}</p></section>@endif
                @if($entry->outcomes)<section class="project-detail-section"><span>{{ $entry->scope ? '02' : '01' }} / RESULTS</span><h2>Key outcomes</h2><p>{{ $entry->outcomes }}</p></section>@endif
                @if(!$projectContent && !$entry->scope && !$entry->outcomes)<div class="project-detail-context"><span>ABOUT THIS RECORD</span><p>This record shares the professional experience behind BEAL’s work. Further project detail can be added as verified information is approved for publication.</p></div>@endif
            </div>
            <aside class="project-detail-aside" aria-label="Project details">
                <p class="project-detail-aside-label">AT A GLANCE</p>
                @if($entry->category)<div><span>Sector</span><strong>{{ $entry->category }}</strong></div>@endif
                @if($entry->location)<div><span>Location</span><strong>{{ $entry->location }}</strong></div>@endif
                @if($entry->scale)<div><span>Project scale</span><strong>{{ $entry->scale }}</strong></div>@endif
                @if(!$entry->category && !$entry->location && !$entry->scale)<p class="project-detail-aside-empty">Project facts will be added as verified details become available.</p>@endif
                <a href="{{ $parentUrl }}">Browse all experience <span aria-hidden="true">↗</span></a>
            </aside>
        </div>
    @else
    <div @class(['secondary-article-aside', 'secondary-sector-aside' => $type === 'sector'])>
        @if($type === 'sector')
            <span class="secondary-sector-aside-label">Sector overview</span>
            <a class="secondary-sector-backlink" href="{{ $parentUrl }}">All sectors <span aria-hidden="true">↗</span></a>
        @else
            <span>{{ $entry->category ?: $parentLabel }}</span>
            @if($entry->published_at)<time datetime="{{ $entry->published_at->toDateString() }}">{{ $entry->published_at->format('F j, Y') }}</time>@endif
            @if($entry->author)<span>By {{ $entry->author }}</span>@endif
            @if($entry->location)<span>{{ $entry->location }}</span>@endif
        @endif
    </div>
    <div @class(['secondary-article-main', 'secondary-sector-main' => $type === 'sector'])>
        @if($type === 'sector')<p class="secondary-sector-main-label">How we support this sector</p>@endif
        @if($type==='project')<p class="secondary-attribution">{{ $entry->experience_source==='beal_engagement'?'BEAL corporate engagement':'Previous professional experience' }}</p>@endif
        @if($entry->content_html)<div class="secondary-rich-content">{!! $entry->content_html !!}</div>@endif
        @if($type==='project' && $entry->scope)<h2>Scope of involvement</h2><p>{{ $entry->scope }}</p>@endif
        @if($type==='project' && $entry->outcomes)<h2>Key outcomes</h2><p>{{ $entry->outcomes }}</p>@endif
    </div>
    @endif
</article>
@if($type === 'project' && $galleryMedia->isNotEmpty())
    <section class="secondary-project-gallery" aria-labelledby="project-gallery-title">
        <div class="project-gallery-inner">
            <div class="secondary-index"><span>PROJECT JOURNEY</span><i></i><span>FROM DESIGN TO DELIVERY</span></div>
            <div class="project-gallery-heading">
                <h2 id="project-gallery-title">A project journey,<br><em>shown in stages.</em></h2>
                <p>AI-generated concept illustrations show how this gallery can follow a project from early plans through design, construction and completion. They are not drawings, site photos or records of this project.</p>
            </div>
            <ol class="project-gallery-path" aria-label="Project stages">
                @foreach($projectGalleryStages as $stageKey => $stageLabel)
                    @continue($stageKey === 'general')
                    @php($stageCount = $galleryMedia->where('project_stage', $stageKey)->count())
                    <li @class(['has-images' => $stageCount > 0])>
                        <a href="#project-stage-{{ $stageKey }}">
                            <span aria-hidden="true">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <div><strong>{{ $stageLabel }}</strong><small>{{ $stageCount > 0 ? $stageCount.' '.\Illuminate\Support\Str::plural('item', $stageCount) : 'To come' }}</small></div>
                        </a>
                    </li>
                @endforeach
            </ol>
            <div class="project-gallery-stage-list">
                @foreach($projectGalleryStages as $stageKey => $stageLabel)
                    @php($stageImages = $galleryMedia->where('project_stage', $stageKey)->values())
                    @if($stageImages->isNotEmpty())
                        <section class="project-gallery-stage" id="project-stage-{{ $stageKey }}">
                            <div class="project-gallery-stage-heading">
                                <div><span>{{ $stageKey === 'general' ? 'ADDITIONAL IMAGERY' : 'STAGE '.str_pad((string) ($loop->iteration), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $stageLabel }}</h3></div>
                                <span>{{ str_pad((string) $stageImages->count(), 2, '0', STR_PAD_LEFT) }} {{ \Illuminate\Support\Str::plural('item', $stageImages->count()) }}</span>
                            </div>
                            <div class="secondary-project-gallery-grid">
                                @foreach($stageImages as $galleryImage)
                                    <figure class="project-gallery-image-card">
                                        <div class="project-gallery-image-frame">
                                            @if(str_starts_with($galleryImage->mime_type, 'video/'))
                                                <video class="project-gallery-video" controls playsinline preload="metadata" aria-label="{{ $galleryImage->alt_text ?: $entry->title.', '.$stageLabel }}">
                                                    <source src="{{ $galleryImage->url }}" type="{{ $galleryImage->mime_type }}">
                                                    Your browser does not support this video.
                                                </video>
                                            @else
                                                <img src="{{ $galleryImage->url }}" alt="{{ $galleryImage->alt_text ?: $entry->title.', '.$stageLabel }}" loading="lazy">
                                            @endif
                                            <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                        </div>
                                        @if(filled($galleryImage->alt_text))<figcaption>{{ $galleryImage->alt_text }}</figcaption>@endif
                                    </figure>
                                @endforeach
                            </div>
                        </section>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
@if($type==='sector')<section class="secondary-related secondary-sector-capabilities section-pad"><div class="secondary-index"><span>BEAL</span><i></i><span>CONNECTED CAPABILITIES</span></div><div class="secondary-related-heading"><h2>Connected expertise<br><em>for development decisions.</em></h2><p>Explore how we bring strategic advice, project management and governance together to support informed decisions from early planning through delivery.</p></div><div class="secondary-related-services">@foreach($services as $service)<a href="{{ route('services.show',$service->slug) }}"><span class="secondary-related-number">{{ $service->number }}</span><div><b>{{ $service->title }}</b><p>{{ $service->description }}</p><span class="secondary-related-link">Explore service <i aria-hidden="true">↗</i></span></div></a>@endforeach</div></section>@endif
@if($type==='insight' && $relatedEntries->isNotEmpty())<section class="secondary-related section-pad"><div class="secondary-index"><span>BEAL</span><i></i><span>RELATED PERSPECTIVES</span></div><div class="secondary-insight-grid">@foreach($relatedEntries as $related)<article class="secondary-insight-card"><div class="secondary-entry-meta">{{ $related->category ?: 'BEAL insight' }} @if($related->published_at)<span>·</span>{{ $related->published_at->format('M Y') }}@endif</div><h2><a href="{{ route('insights.show',$related->slug) }}">{{ $related->title }}</a></h2><p>{{ $related->summary }}</p><a class="text-link text-link-navy" href="{{ route('insights.show',$related->slug) }}">Read insight <span aria-hidden="true">↗</span></a></article>@endforeach</div></section>@endif
<section class="secondary-cta"><div class="section-pad"><div class="secondary-index secondary-index-light"><span>BEAL</span><i></i><span>START A CONVERSATION</span></div><div class="secondary-cta-layout"><div><h2>{{ $settings['presentation.internal_cta_heading'] ?? 'Explore the capabilities' }}<br><em>{{ $settings['presentation.internal_cta_accent'] ?? 'behind the perspective.' }}</em></h2><p>{{ $settings['presentation.internal_cta_description'] ?? 'Connect with us about complex project and infrastructure decisions.' }}</p></div><button class="button button-gold" type="button" data-enquiry-open>{{ $settings['presentation.internal_cta_button'] ?? 'Start a conversation' }} <span aria-hidden="true">↗</span></button></div></div></section>
@endsection
