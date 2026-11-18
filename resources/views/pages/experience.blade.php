@extends('layouts.public')
@section('content')
<section class="secondary-hero secondary-hero-experience" style="--secondary-image:url('{{ $sectionImages['experience_intro'] }}')"><div class="secondary-hero-shade"></div><div class="secondary-hero-inner"><x-breadcrumb :items="[['label'=>'Projects & Experience']]"/><p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>{{ $content['experience_intro']['hero_eyebrow'] ?? 'PROJECTS &amp; EXPERIENCE' }}</p><h1>{{ $page->title }}</h1><p class="secondary-hero-subtitle">{{ $content['experience_intro']['hero_subtitle'] ?? 'Professional contribution, presented with context.' }}</p></div><span class="secondary-hero-index">BEAL / 04</span></section>
@if(($visible['experience_intro'] ?? true) && isset($content['experience_intro']))<section class="secondary-intro section-pad"><div class="secondary-index"><span>01</span><i></i><span>EXPERIENCE FRAMEWORK</span></div><div class="secondary-intro-grid"><h2>{{ $content['experience_intro']['heading'] ?? 'Experience, with context.' }}</h2><div>{!! $content['experience_intro']['introduction'] ?? '' !!}</div></div></section>@endif
<section class="experience-records-section section-pad" aria-labelledby="experience-records-title">
    <div class="experience-records-heading">
        <div>
            <div class="secondary-index"><span>02</span><i></i><span>SELECTED EXPERIENCE</span></div>
            <h2 id="experience-records-title">Project experience,<br><em>clearly presented.</em></h2>
            <p>Browse selected project records. Each entry identifies whether the work was delivered by BEAL or reflects experience gained in an earlier role.</p>
        </div>
        @if($entries->isNotEmpty())
            <div class="experience-record-count" aria-label="{{ $entries->count() }} project records">
                <strong>{{ str_pad((string) $entries->count(), 2, '0', STR_PAD_LEFT) }}</strong>
                <span>{{ \Illuminate\Support\Str::plural('project record', $entries->count()) }}</span>
            </div>
        @endif
    </div>

    @if($entries->isNotEmpty())
        <div class="experience-project-grid">
            @foreach($entries as $entry)
                @php($summary = trim(strip_tags((string) ($entry->summary ?? ''))))
                @php($showSummary = $summary !== '' && ! \Illuminate\Support\Str::startsWith($summary, 'Project experience gained by'))
                <article class="experience-project-card{{ $loop->first ? ' experience-project-card-featured' : '' }}">
                    @if($entry->banner_url)
                        <a class="experience-project-art experience-project-art-image" href="{{ route('experience.show', $entry->slug) }}" aria-label="View {{ $entry->title }}">
                            <img src="{{ $entry->banner_url }}" alt="{{ $entry->image_alt }}" loading="lazy" width="1200" height="800">
                            <span class="experience-project-art-caption">BEAL / PROJECT RECORD</span>
                        </a>
                    @else
                        <div class="experience-project-art" data-variant="{{ $loop->iteration % 3 }}" aria-hidden="true">
                            <span class="experience-project-art-number">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            <span class="experience-project-art-grid"></span>
                            <span class="experience-project-art-arch"></span>
                            <span class="experience-project-art-caption">BEAL / PROJECT RECORD</span>
                        </div>
                    @endif
                    <div class="experience-project-content">
                        <div class="experience-project-tags">
                            <span>{{ $entry->experience_source === 'beal_engagement' ? 'BEAL engagement' : 'Previous professional experience' }}</span>
                            @if($entry->category)<span>{{ $entry->category }}</span>@endif
                        </div>
                        <h3><a href="{{ route('experience.show', $entry->slug) }}">{{ $entry->title }}</a></h3>
                        @if($showSummary)<p class="experience-project-summary">{{ $summary }}</p>@endif
                        @if($entry->location || $entry->scale)
                            <div class="experience-project-details">
                                @if($entry->location)<span>{{ $entry->location }}</span>@endif
                                @if($entry->scale)<span>Scale: {{ $entry->scale }}</span>@endif
                            </div>
                        @endif
                        <a class="experience-project-action" href="{{ route('experience.show', $entry->slug) }}">View project record <span aria-hidden="true">↗</span></a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="secondary-empty-state secondary-empty-experience">
            <span class="secondary-index"><span>BEAL</span><i></i><span>PROJECT RECORD</span></span>
            <h2>Experience, presented<br><em>with context.</em></h2>
            <p>{{ $content['experience_intro']['publishing_note'] ?? 'No project or experience records have been approved for publication yet.' }}</p>
            <p>Future entries can distinguish engagements delivered by BEAL from previous professional experience. Client, scope, location, scale and outcomes can be included when verified and approved.</p>
            <a class="button button-dark" href="{{ route('pages.contact') }}">Start a conversation <span aria-hidden="true">↗</span></a>
        </div>
    @endif
</section>
@endsection
