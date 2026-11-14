<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1b31">
    <meta name="description" content="{{ $pageDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ asset($service['image']) }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/beal-logo.png') }}">
    <title>{{ $pageTitle }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="image-home service-detail-page">
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="topline">
        <div class="topline-inner">
            <span class="topline-location"><span class="location-mark" aria-hidden="true"></span>Nairobi, Kenya <span class="topline-divider">/</span> Across Africa</span>
            <span class="topline-note">Independent thinking. Informed decisions.</span>
            <span class="topline-index">Strategic advisory <span>—</span> Infrastructure intelligence</span>
        </div>
    </div>
    <header class="site-header" data-header>
        <div class="header-inner">
            <a class="brand" href="{{ route('home') }}" aria-label="BEAL Consulting home">
                <img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting — Precision in Every Project" width="2167" height="726">
            </a>
            <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span></span><span></span><span></span>
            </button>
            <nav class="navigation" id="primary-navigation" aria-label="Main navigation" data-navigation>
                <a href="{{ route('home') }}#about">About</a>
                <a href="{{ route('home') }}#services" aria-current="page">Services</a>
                <a href="{{ route('home') }}#sectors">Sectors</a>
                <a href="{{ route('home') }}#insights">Insights</a>
                <a href="{{ route('home') }}#experience">Experience</a>
            </nav>
            <a class="button button-small button-dark header-cta" href="{{ route('home') }}#contact">Start a conversation <span aria-hidden="true">↗</span></a>
        </div>
    </header>

    <main id="main">
        <section class="service-detail-hero" aria-labelledby="service-title">
            <img src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] }}" width="1500" height="1000" fetchpriority="high" decoding="async">
            <div class="service-detail-hero-shade" aria-hidden="true"></div>
            <div class="service-detail-hero-inner">
                <nav class="service-breadcrumb" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span aria-hidden="true">/</span><a href="{{ route('home') }}#services">Our Services</a><span aria-hidden="true">/</span><span>{{ $service['title'] }}</span>
                </nav>
                <div class="service-detail-heading">
                    <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span><span>SERVICE PILLAR {{ $service['number'] }}</span></p>
                    <h1 id="service-title">{{ $service['title'] }}</h1>
                    <p>{{ $service['description'] }}</p>
                    <a class="button button-gold" href="#service-scope">Explore this service <span aria-hidden="true">↓</span></a>
                </div>
            </div>
            <div class="service-detail-hero-caption"><span>BEAL CONSULTING</span><span>Clarity in Projects. Confidence in Decisions.</span></div>
        </section>

        <section class="service-scope section-pad" id="service-scope" aria-labelledby="service-scope-title">
            <div class="section-index"><span>{{ $service['number'] }}</span><span class="index-rule"></span><span>AREAS OF FOCUS</span></div>
            <div class="service-scope-heading">
                <h2 id="service-scope-title">A considered view<br><em>of what matters.</em></h2>
                <p>{{ $service['description'] }} The service is shaped around the context, information and decisions involved in each project.</p>
            </div>
            <div class="service-scope-list">
                @foreach ($service['subservices'] as $index => $subservice)
                    <div class="service-scope-item"><span>{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><h3>{{ $subservice }}</h3><span class="service-scope-mark" aria-hidden="true">↗</span></div>
                @endforeach
            </div>
        </section>

        <section class="service-related section-pad" aria-labelledby="service-related-title">
            <div class="section-index"><span>BEAL</span><span class="index-rule"></span><span>CONNECTED DISCIPLINES</span></div>
            <div class="service-related-heading"><h2 id="service-related-title">Perspective across<br><em>the project lifecycle.</em></h2><p>Explore the other service pillars that bring connected intelligence to complex projects and infrastructure decisions.</p></div>
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
                <div class="section-index section-index-light"><span>BEAL</span><span class="index-rule"></span><span>START A CONVERSATION</span></div>
                <div class="service-detail-cta-row"><h2>Bring clarity to<br><em>the next decision.</em></h2><div><p>For an enquiry about {{ $service['title'] }}, connect with the BEAL team.</p><a class="button button-gold" href="{{ route('home') }}#contact">Get in touch <span aria-hidden="true">↗</span></a></div></div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-main"><a class="footer-brand" href="{{ route('home') }}"><img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting" width="2167" height="726"></a><div class="footer-statement">Independent thinking.<br><em>Informed decisions.</em></div><div class="footer-nav"><span>EXPLORE</span><a href="{{ route('home') }}#about">About BEAL</a><a href="{{ route('home') }}#services">Our Services</a><a href="{{ route('home') }}#insights">Insights &amp; Intelligence</a></div><div class="footer-nav"><span>CONNECT</span><a href="{{ route('home') }}#contact">Contact BEAL</a><span>Nairobi, Kenya</span></div></div>
        <div class="footer-legal"><span>© {{ date('Y') }} BEAL Consulting Ltd</span><span>Precision in Every Project.</span><a href="{{ route('home') }}#top">Back to top ↑</a></div>
    </footer>
</body>
</html>
