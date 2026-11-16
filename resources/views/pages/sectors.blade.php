@extends('layouts.public')
@section('content')
<section class="secondary-hero secondary-hero-sectors" style="--secondary-image:url('{{ $sectionImages['sectors_intro'] }}')"><div class="secondary-hero-shade"></div><div class="secondary-hero-inner"><x-breadcrumb :items="[['label'=>'Sectors']]"/><p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span>{{ $content['sectors_intro']['hero_eyebrow'] ?? 'SECTOR PERSPECTIVES' }}</p><h1>{{ $page->title }}</h1><p class="secondary-hero-subtitle">{{ $content['sectors_intro']['hero_subtitle'] ?? 'A connected view of Africa' }}</p></div><span class="secondary-hero-index">BEAL / 02</span></section>
@if(($visible['sectors_intro'] ?? true) && isset($content['sectors_intro']))<section class="secondary-intro section-pad"><div class="secondary-index"><span>01</span><i></i><span>INTENDED SECTOR COVERAGE</span></div><div class="secondary-intro-grid"><h2>{{ $content['sectors_intro']['heading'] ?? 'Sector perspectives.' }}</h2><div>{!! $content['sectors_intro']['introduction'] ?? '' !!}</div></div></section>@endif
<section class="secondary-list-section secondary-sector-directory section-pad" aria-labelledby="sector-directory-title">
    <div class="sector-directory-heading">
        <div>
            <p class="sector-directory-kicker">OUR SECTOR COVERAGE</p>
            <h2 id="sector-directory-title">Six sectors. <em>Connected decisions.</em></h2>
        </div>
        <span class="sector-directory-count"><b>{{ str_pad((string) $entries->count(), 2, '0', STR_PAD_LEFT) }}</b> SECTORS</span>
    </div>
    <div class="secondary-sector-grid">
        @foreach($entries as $index => $entry)
            <a class="secondary-sector-card" href="{{ route('sectors.show', $entry->slug) }}">
                <span class="secondary-card-index">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} <i>/ {{ str_pad((string) $entries->count(), 2, '0', STR_PAD_LEFT) }}</i></span>
                <h3>{{ $entry->title }}</h3>
                <p>{{ $entry->summary }}</p>
                <span class="secondary-card-link">Explore sector <b aria-hidden="true">↗</b></span>
            </a>
        @endforeach
    </div>
    <p class="secondary-note">Explore our perspectives across six sectors, and see how our capabilities support informed project decisions.</p>
</section>
<section class="secondary-cta"><div class="section-pad"><div class="secondary-index secondary-index-light"><span>BEAL</span><i></i><span>CONNECTED CAPABILITIES</span></div><div class="secondary-cta-layout"><div><h2>{{ $settings['presentation.internal_cta_heading'] ?? 'Explore the capabilities' }}<br><em>{{ $settings['presentation.internal_cta_accent'] ?? 'behind the perspective.' }}</em></h2><p>{{ $settings['presentation.internal_cta_description'] ?? 'Connect with us about complex project and infrastructure decisions.' }}</p></div><a class="button button-gold" href="{{ route('home') }}#services">{{ $settings['presentation.internal_cta_button'] ?? 'Explore our services' }} <span aria-hidden="true">↗</span></a></div></div></section>
@endsection
