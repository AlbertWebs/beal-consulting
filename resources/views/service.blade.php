<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1b31">
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="robots" content="{{ ($isPreview ?? false) || ! config('beal.indexing_enabled') ? 'noindex,nofollow' : ($settings['seo.robots'] ?? 'index,follow') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ ($settings['branding.social_sharing_image'] ?? '') ?: (str_starts_with($service['image'], '/') ? url($service['image']) : asset($service['image'])) }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ ($settings['branding.favicon'] ?? '') ?: asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/beal-logo.png') }}">
    <title>{{ $pageTitle }}</title>
    <style>:root{--navy:{{ $settings['branding.primary_color'] ?? '#0b1b31' }};--gold:{{ $settings['branding.accent_color'] ?? '#c8a15d' }}}</style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="image-home service-detail-page">
    @if($isPreview ?? false)<div role="status" style="position:fixed;z-index:100;right:14px;top:14px;background:#c8a15d;color:#0b1b31;padding:8px 12px;font-size:13px">Authenticated draft preview · not published</div>@endif
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="topline">
        <div class="topline-inner">
            <span class="topline-location"><span class="location-mark" aria-hidden="true"></span>{{ $settings['header.announcement'] ?? 'Nairobi, Kenya / Across Africa' }}</span>
            <span class="topline-note">{{ $settings['general.tagline'] ?? 'Independent thinking. Informed decisions.' }}</span>
            <span class="topline-index">{{ $settings['header.positioning'] ?? 'Strategic advisory - Infrastructure intelligence' }}</span>
        </div>
    </div>
    <header class="site-header" data-header>
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="BEAL Consulting home">
                <img src="{{ ($settings['branding.horizontal_logo'] ?? '') ?: asset('assets/beal-horizontal-logo.png') }}" alt="{{ $settings['general.site_name'] ?? 'BEAL Consulting' }}" width="2167" height="726">
            </a>
            <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span></span><span></span><span></span>
            </button>
            <nav class="navigation" id="primary-navigation" aria-label="Main navigation" data-navigation>
                @foreach(($settings['header.navigation'] ?? []) as $item) @if($item['enabled'] ?? true)<a href="{{ str_starts_with($item['url'],'#') ? route('home').$item['url'] : $item['url'] }}" @if($item['url']==='#services') aria-current="page" @endif>{{ $item['label'] }}</a>@endif @endforeach
            </nav>
            <a class="button button-small button-dark header-cta" href="{{ str_starts_with($settings['header.cta_url'] ?? '#contact','#') ? route('home').($settings['header.cta_url'] ?? '#contact') : ($settings['header.cta_url'] ?? route('home').'#contact') }}">{{ $settings['header.cta_label'] ?? 'Start a conversation' }} <span aria-hidden="true">↗</span></a>
        </div>
    </header>

    <main id="main">
        <section class="service-detail-hero" aria-labelledby="service-title">
            <img src="{{ str_starts_with($service['image'], '/') ? $service['image'] : asset($service['image']) }}" alt="{{ $service['image_alt'] }}" width="1500" height="1000" fetchpriority="high" decoding="async">
            <div class="service-detail-hero-shade" aria-hidden="true"></div>
            <div class="service-detail-hero-inner">
                <nav class="service-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">{{ $settings['general.site_name'] ?? 'Home' }}</a><span aria-hidden="true">/</span><a href="{{ route('home') }}#services">{{ collect($settings['header.navigation'] ?? [])->firstWhere('url', '#services')['label'] ?? 'Our Services' }}</a><span aria-hidden="true">/</span><span>{{ $service['title'] }}</span>
                </nav>
                <div class="service-detail-heading">
                    <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span><span>{{ $service['content']['service_pillar_label'] ?? 'SERVICE PILLAR' }} {{ $service['number'] }}</span></p>
                    <h1 id="service-title">{{ $service['title'] }}</h1>
                    <div class="service-description">{!! $service['description_html'] ?? e($service['description']) !!}</div>
                    <a class="button button-gold" href="#service-scope">{{ $service['content']['hero_button_label'] ?? 'Explore this service' }} <span aria-hidden="true">↓</span></a>
                </div>
            </div>
            <div class="service-detail-hero-caption"><span>{{ $settings['general.site_name'] ?? 'BEAL Consulting' }}</span><span>{{ $service['content']['hero_caption'] ?? 'Clarity in Projects. Confidence in Decisions.' }}</span></div>
        </section>

        <section class="service-scope section-pad" id="service-scope" aria-labelledby="service-scope-title">
            @if(!empty(config('cms.service_introductions.'.$service['slug'])))
                <div class="service-scope-intro">
                    @foreach(config('cms.service_introductions.'.$service['slug']) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>
            @endif
            <div class="section-index"><span>{{ $service['number'] }}</span><span class="index-rule"></span><span>{{ $service['content']['scope_label'] ?? 'AREAS OF FOCUS' }}</span></div>
            <div class="service-scope-heading">
                <h2 id="service-scope-title">{{ $service['content']['scope_heading'] ?? 'A considered view' }}<br><em>{{ $service['content']['scope_heading_accent'] ?? 'of what matters.' }}</em></h2>
                <p>{{ $service['content']['scope_description'] ?? 'The service is shaped around the context, information and decisions involved in each project.' }}</p>
            </div>
            <div class="service-scope-list">
                @foreach ($service['subservices'] as $index => $subservice)
                    <div class="service-scope-item"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $subservice }}</h3><span class="service-scope-mark" aria-hidden="true">↗</span></div>
                @endforeach
            </div>
        </section>

        @if ($service['slug'] === 'governance-risk-performance')
            <section class="service-scorecard section-pad" id="service-scorecard" aria-labelledby="service-scorecard-title">
                <div class="section-index"><span>03</span><span class="index-rule"></span><span>PROJECT HEALTH SCORECARD</span></div>
                <div class="service-scorecard-heading">
                    <div>
                        <p class="eyebrow eyebrow-navy"><span class="eyebrow-rule"></span>PROJECT HEALTH ASSESSMENT</p>
                        <h2 id="service-scorecard-title">The BEAL Project Health<br><em>Scorecard</em></h2>
                        <p>Across eight core areas, including cost, schedule, quality, procurement, commercial, HSE, governance and delivery, we examine scope, risk and the decisions affecting project performance. Our scorecard shows what is working well, where concerns are emerging and what management action is needed.</p>
                    </div>
                </div>
                <x-scorecard-assessment-grid />
                <div class="service-scorecard-deliverables">
                    <div><span>01</span><h3>One view of project health</h3><p>Overall rating with a view of performance across the eight assessment areas.</p></div>
                    <div><span>02</span><h3>Issues that need attention</h3><p>Key risks, delays, cost concerns and decisions surfaced for management review.</p></div>
                    <div><span>03</span><h3>Clear next actions</h3><p>Recommended actions with responsible parties and target dates.</p></div>
                </div>
                <div class="service-scorecard-actions">
                    <a class="button button-gold" href="{{ route('scorecard.assessment') }}">Request a Project Health Assessment <span aria-hidden="true">↗</span></a>
                    <a class="button button-outline" href="{{ route('scorecard.samples') }}">View sample scorecards <span aria-hidden="true">↗</span></a>
                </div>
            </section>
        @endif

        <section class="service-related section-pad" aria-labelledby="service-related-title">
            <div class="section-index"><span>BEAL</span><span class="index-rule"></span><span>{{ $service['content']['related_label'] ?? 'CONNECTED DISCIPLINES' }}</span></div>
            <div class="service-related-heading"><h2 id="service-related-title">{{ $service['content']['related_heading'] ?? 'Perspective across' }}<br><em>{{ $service['content']['related_heading_accent'] ?? 'the project lifecycle.' }}</em></h2><p>{{ $service['content']['related_description'] ?? 'Explore the other service pillars that bring connected intelligence to complex projects and infrastructure decisions.' }}</p></div>
            <div class="service-related-grid">
                @foreach ($otherServices as $relatedService)
                    <a class="service-related-card" href="{{ route('services.show', ['service' => $relatedService['slug']]) }}">
                        <span>{{ $relatedService['number'] }}</span><h3>{{ $relatedService['title'] }}</h3><p>{{ $relatedService['description'] }}</p><span class="service-related-arrow" aria-hidden="true">↗</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="service-detail-cta">
            <div class="service-detail-cta-inner">
                <div class="section-index section-index-light"><span>BEAL</span><span class="index-rule"></span><span>{{ $service['content']['cta_label'] ?? 'START A CONVERSATION' }}</span></div>
                <div class="service-detail-cta-row"><h2>{{ $service['content']['cta_heading'] ?? 'Bring clarity to' }}<br><em>{{ $service['content']['cta_heading_accent'] ?? 'the next decision.' }}</em></h2><div><p>{{ $service['content']['cta_description'] ?? 'Tell us what you need and we will follow up.' }}</p><button class="button button-gold" type="button" data-enquiry-open data-enquiry-service="{{ $service['title'] }}">Get in touch <span aria-hidden="true">↗</span></button></div></div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        @php($publicEmail = $settings['contact.email'] ?? $settings['general.public_email'] ?? '')
        @php($publicPhone = $settings['contact.phone'] ?? $settings['general.phone'] ?? '')
        @php($publicLocation = $settings['contact.location'] ?? $settings['general.address'] ?? 'Nairobi, Kenya')
        @php($publicMapLink = $settings['contact.maps_url'] ?? $settings['general.maps_url'] ?? '')
        @php($publicHours = $settings['contact.business_hours'] ?? $settings['general.business_hours'] ?? '')
        <div class="footer-main"><a class="footer-brand" href="{{ route('home') }}"><img src="{{ ($settings['branding.horizontal_logo'] ?? '') ?: asset('assets/beal-horizontal-logo.png') }}" alt="{{ $settings['general.site_name'] ?? 'BEAL Consulting' }}" width="2167" height="726"></a><div class="footer-statement">{{ $settings['footer.statement'] ?? 'Independent thinking. Informed decisions.' }}</div><div class="footer-nav"><span>{{ $settings['footer.explore_label'] ?? 'EXPLORE' }}</span>@foreach(($settings['footer.navigation'] ?? []) as $item) @if($item['enabled'] ?? true)<a href="{{ str_starts_with($item['url'],'#') ? route('home').$item['url'] : $item['url'] }}">{{ $item['label'] }}</a>@endif @endforeach</div><div class="footer-nav"><span>{{ $settings['footer.contact_label'] ?? 'CONNECT' }}</span><a href="{{ filled($publicEmail) ? 'mailto:'.$publicEmail : route('home').'#contact' }}">{{ $publicEmail ?: 'Contact BEAL' }}</a>@if(filled($publicPhone))<a href="tel:{{ preg_replace('/[^+0-9]/', '', $publicPhone) }}">{{ $publicPhone }}</a>@endif @if(filled($publicLocation))<span>{{ $publicLocation }}</span>@endif @if(filled($publicMapLink))<a href="{{ $publicMapLink }}" rel="noopener noreferrer" target="_blank">View location <span aria-hidden="true">↗</span></a>@endif @if(filled($publicHours))<span>{{ $publicHours }}</span>@endif @foreach(($settings['footer.social_links'] ?? []) as $social) @if(($social['enabled'] ?? true) && filled($social['url'] ?? ''))<a href="{{ $social['url'] }}" rel="noopener noreferrer" target="_blank">{{ $social['label'] ?? 'Social' }}</a>@endif @endforeach</div></div>
        <div class="footer-legal"><span>{{ str_replace(':year', date('Y'), $settings['general.copyright'] ?? '© :year BEAL Consulting Ltd') }}</span><span>Precision in Every Project.</span><a href="{{ route('home') }}#top">Back to top ↑</a></div>
    </footer>
    <x-enquiry-dialog />
</body>
</html>
