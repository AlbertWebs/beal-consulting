@extends('layouts.public')

@section('head')
    <script type="application/ld+json">{!! $personJsonLd !!}</script>
@endsection

@section('content')
    <section class="secondary-hero secondary-hero-about" style="--secondary-image:url('{{ $bannerImage }}')">
        <div class="secondary-hero-shade"></div>
        <div class="secondary-hero-inner">
            <x-breadcrumb :items="[['label' => 'About BEAL', 'url' => route('pages.about')], ['label' => 'Team', 'url' => route('pages.about').'#leadership'], ['label' => $profile['name']]]" />
            <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>BEAL TEAM</p>
            <h1>{{ $profile['name'] }}</h1>
            <p class="secondary-hero-subtitle">@if (filled($profileDesignation))<strong>{{ $profileDesignation }}</strong><span aria-hidden="true"> · </span>@endif{{ $profileRole }}</p>
        </div>
        <span class="secondary-hero-index">BEAL / TEAM</span>
    </section>

    @php($profileSections = collect($profile['sections'] ?? [])->filter(fn (mixed $section): bool => is_array($section) && ((is_string($section['heading'] ?? null) && filled($section['heading'])) || (is_string($section['body'] ?? null) && filled($section['body'])) || (is_array($section['paragraphs'] ?? null) && ! empty($section['paragraphs'])))) )
    @php($profileCredentials = collect($profile['credentials'] ?? [])->filter(fn (mixed $item): bool => is_string($item) && filled(trim($item))))
    @php($profileSpecialisms = collect($profile['specialisms'] ?? [])->filter(fn (mixed $item): bool => is_string($item) && filled(trim($item))))
    <section class="team-profile-main section-pad">
        <div class="team-profile-layout">
            <aside class="team-profile-sidebar">
                @if (filled($profile['image'] ?? ''))
                    <div @class(['team-profile-portrait', 'team-profile-portrait-douglas' => $profileSlug === 'douglas-murithi-kathuri'])><img src="{{ $profile['image'] }}" alt="{{ $profile['image_alt'] ?? 'Portrait of '.$profile['name'] }}" width="720" height="900"></div>
                @else
                    <div class="team-profile-portrait team-profile-portrait-placeholder" aria-hidden="true"><span>{{ collect(preg_split('/\s+/', trim((string) $profile['name'])))->map(fn (string $part): string => mb_substr($part, 0, 1))->take(2)->implode('') }}</span></div>
                @endif
                <div class="team-profile-sidebar-copy">
                    <span class="team-profile-kicker">BEAL CONSULTING</span>
                    <h2>{{ $profile['name'] }}</h2>
                    <p>@if (filled($profileDesignation))<strong>{{ $profileDesignation }}</strong><span aria-hidden="true"> · </span>@endif{{ $profileRole }}</p>
                    @if ($profileCredentials->isNotEmpty())
                        <div class="team-profile-facts"><h3>Qualifications</h3><ul>@foreach ($profileCredentials as $credential)<li>{{ $credential }}</li>@endforeach</ul></div>
                    @endif
                    <a class="text-link text-link-navy" href="{{ route('pages.contact') }}">Contact BEAL <span aria-hidden="true">↗</span></a>
                </div>
            </aside>

            <article class="team-profile-copy">
                <div class="secondary-index"><span>BEAL</span><i></i><span>PROFESSIONAL PROFILE</span></div>
                @if (filled($profile['introduction'] ?? ''))
                    <p class="team-profile-introduction">{{ $profile['introduction'] }}</p>
                @endif
                <h2>About {{ $profile['name'] }}</h2>
                <div class="team-profile-biography">
                    @forelse ($profileBio as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @empty
                        <p>{{ $profile['name'] }} is part of BEAL Consulting’s professional team, contributing in the role of {{ $profileRole }}.</p>
                    @endforelse
                </div>

                @if ($profileSpecialisms->isNotEmpty())
                    <section class="team-profile-subsection"><h2>Areas of focus</h2><ul class="team-profile-specialisms">@foreach ($profileSpecialisms as $specialism)<li>{{ $specialism }}</li>@endforeach</ul></section>
                @endif

                @foreach ($profileSections as $section)
                    <section class="team-profile-subsection">
                        @if (is_string($section['heading'] ?? null) && filled($section['heading']))<h2>{{ $section['heading'] }}</h2>@endif
                        @if (is_string($section['body'] ?? null) && filled($section['body']))<p>{{ $section['body'] }}</p>@endif
                        @foreach ((is_array($section['paragraphs'] ?? null) ? $section['paragraphs'] : []) as $paragraph)
                            @if (is_string($paragraph) && filled(trim($paragraph)))<p>{{ $paragraph }}</p>@endif
                        @endforeach
                    </section>
                @endforeach

                <a class="team-profile-backlink" href="{{ route('pages.about') }}#leadership">← Meet the wider team</a>
            </article>
        </div>
    </section>

    <section class="secondary-cta">
        <div class="section-pad">
            <div class="secondary-index secondary-index-light"><span>BEAL</span><i></i><span>PROJECT HEALTH SCORECARD</span></div>
            <div class="secondary-cta-layout">
                <div><h2>Bring project performance<br><em>into clear view.</em></h2><p>Talk with BEAL about project health, delivery risks and practical corrective action.</p></div>
                <a class="button button-gold" href="{{ route('services.show', ['service' => 'governance-risk-performance']).'#service-scorecard' }}">Explore the Scorecard <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>
@endsection
