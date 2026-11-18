@extends('layouts.public', ['ogType' => $type === 'insight' ? 'article' : 'website'])
@section('content')
@php($parentLabel = $type==='sector'?'Sectors':($type==='insight'?'Insights & Intelligence':'Projects & Experience'))
@php($parentUrl = route('pages.'.($type==='sector'?'sectors':($type==='insight'?'insights':'experience'))))
<section class="secondary-hero secondary-entry-hero" style="--secondary-image:url('{{ $bannerImage }}')"><div class="secondary-hero-shade"></div><div class="secondary-hero-inner"><x-breadcrumb :items="[['label'=>$parentLabel,'url'=>$parentUrl],['label'=>$entry->title]]"/><p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>{{ $type==='sector'?'SECTOR PERSPECTIVE':($type==='insight'?'INSIGHTS & INTELLIGENCE':($entry->experience_source==='beal_engagement'?'BEAL ENGAGEMENT':'PREVIOUS PROFESSIONAL EXPERIENCE')) }}</p><h1>{{ $entry->title }}</h1>@if($entry->summary)<p class="secondary-hero-subtitle">{{ $entry->summary }}</p>@endif</div><span class="secondary-hero-index">BEAL / {{ $type==='sector'?'SECTOR':($type==='insight'?'PERSPECTIVE':'EXPERIENCE') }}</span></section>
<article @class(['secondary-article', 'section-pad', 'secondary-sector-article' => $type === 'sector'])>
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
</article>
@if($type==='project' && $galleryMedia->isNotEmpty())<section class="secondary-project-gallery section-pad"><div class="secondary-index"><span>BEAL</span><i></i><span>PROJECT GALLERY</span></div><div class="secondary-project-gallery-grid">@foreach($galleryMedia as $galleryImage)<figure><img src="{{ $galleryImage->url }}" alt="{{ $galleryImage->alt_text ?: 'Project image for '.$entry->title }}" loading="lazy"><figcaption>{{ $galleryImage->alt_text ?: $entry->title }}</figcaption></figure>@endforeach</div></section>@endif
@if($type==='sector')<section class="secondary-related secondary-sector-capabilities section-pad"><div class="secondary-index"><span>BEAL</span><i></i><span>CONNECTED CAPABILITIES</span></div><div class="secondary-related-heading"><h2>Connected expertise<br><em>for development decisions.</em></h2><p>Explore how we bring strategic advice, project management and governance together to support informed decisions from early planning through delivery.</p></div><div class="secondary-related-services">@foreach($services as $service)<a href="{{ route('services.show',$service->slug) }}"><span class="secondary-related-number">{{ $service->number }}</span><div><b>{{ $service->title }}</b><p>{{ $service->description }}</p><span class="secondary-related-link">Explore service <i aria-hidden="true">↗</i></span></div></a>@endforeach</div></section>@endif
@if($type==='insight' && $relatedEntries->isNotEmpty())<section class="secondary-related section-pad"><div class="secondary-index"><span>BEAL</span><i></i><span>RELATED PERSPECTIVES</span></div><div class="secondary-insight-grid">@foreach($relatedEntries as $related)<article class="secondary-insight-card"><div class="secondary-entry-meta">{{ $related->category ?: 'BEAL insight' }} @if($related->published_at)<span>·</span>{{ $related->published_at->format('M Y') }}@endif</div><h2><a href="{{ route('insights.show',$related->slug) }}">{{ $related->title }}</a></h2><p>{{ $related->summary }}</p><a class="text-link text-link-navy" href="{{ route('insights.show',$related->slug) }}">Read insight <span aria-hidden="true">↗</span></a></article>@endforeach</div></section>@endif
<section class="secondary-cta"><div class="section-pad"><div class="secondary-index secondary-index-light"><span>BEAL</span><i></i><span>START A CONVERSATION</span></div><div class="secondary-cta-layout"><div><h2>{{ $settings['presentation.internal_cta_heading'] ?? 'Explore the capabilities' }}<br><em>{{ $settings['presentation.internal_cta_accent'] ?? 'behind the perspective.' }}</em></h2><p>{{ $settings['presentation.internal_cta_description'] ?? 'Connect with us about complex project and infrastructure decisions.' }}</p></div><button class="button button-gold" type="button" data-enquiry-open>{{ $settings['presentation.internal_cta_button'] ?? 'Start a conversation' }} <span aria-hidden="true">↗</span></button></div></div></section>
@endsection
