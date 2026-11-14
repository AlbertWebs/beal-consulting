<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1b31">
    <meta name="description" content="BEAL Consulting brings intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="BEAL Consulting | Clarity in Projects. Confidence in Decisions.">
    <meta property="og:description" content="Intelligence-led strategic advisory and infrastructure consultancy across East Africa and the wider African market.">
    <meta property="og:image" content="{{ asset('assets/home-image/nairobi-skyline.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/beal-logo.png') }}">
    <title>BEAL Consulting | Clarity in Projects. Confidence in Decisions.</title>
    <script type="application/ld+json">{!! $organizationJson !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="image-home">
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
            <a class="brand" href="#top" aria-label="BEAL Consulting home">
                <img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting — Precision in Every Project" width="2167" height="726">
            </a>
            <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span></span><span></span><span></span>
            </button>
            <nav class="navigation" id="primary-navigation" aria-label="Main navigation" data-navigation>
                <a href="#about">About</a>
                <a href="#services">Services</a>
                <a href="#sectors">Sectors</a>
                <a href="#insights">Insights</a>
                <a href="#experience">Experience</a>
            </nav>
            <a class="button button-small button-dark header-cta" href="#contact" data-contact-cta>Start a conversation <span aria-hidden="true">↗</span></a>
        </div>
    </header>

    <main id="main">
        <section class="image-hero" id="top" aria-labelledby="hero-title">
            <img class="image-hero-photo" src="{{ asset('assets/home-image/nairobi-skyline.jpg') }}" alt="Nairobi skyline rising above the city's green canopy" width="2200" height="1237" fetchpriority="high" decoding="async">
            <div class="image-hero-shade" aria-hidden="true"></div>
            <div class="image-hero-grid" aria-hidden="true"></div>
            <div class="image-hero-inner">
                <div class="image-hero-copy">
                    <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span><span>BEAL CONSULTING LTD</span><span class="eyebrow-divider">/</span><span>NAIROBI, KENYA</span></p>
                    <h1 id="hero-title">Clarity in Projects.<br><em>Confidence in Decisions.</em></h1>
                    <p class="image-hero-summary">We bring intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions.</p>
                    <div class="hero-actions">
                        <a class="button button-gold" href="#services">Explore our expertise <span aria-hidden="true">↗</span></a>
                        <a class="text-link text-link-light" href="#about">Discover BEAL <span aria-hidden="true">↓</span></a>
                    </div>
                </div>
                <div class="image-hero-note"><span>01 / 05</span><span>Strategic perspective<br>for a changing Africa</span></div>
            </div>
            <div class="image-hero-caption"><span>NAIROBI, KENYA</span><span>Independent thinking. Informed decisions.</span></div>
        </section>

        <section class="image-intro section-pad" id="about" aria-labelledby="intro-title">
            <div class="section-index"><span>01</span><span class="index-rule"></span><span>THE BEAL PERSPECTIVE</span></div>
            <div class="image-intro-grid">
                <div class="image-intro-copy">
                    <h2 id="intro-title">Complexity deserves <em>clear thinking.</em></h2>
                    <p class="lead-copy">BEAL is an intelligence-led strategic advisory and infrastructure consultancy, bringing analytical capability and technical perspective to the decisions that shape projects.</p>
                    <p>We look beyond the immediate task to understand context, connect information and help decision-makers move forward with confidence.</p>
                    <a class="text-link text-link-navy" href="#services">How we work <span aria-hidden="true">↗</span></a>
                </div>
                <figure class="image-intro-photo">
                    <img src="{{ asset('assets/home-image/glass-architecture.jpg') }}" alt="Sculptural glass architecture framed against a clear sky" width="1500" height="1000" loading="lazy" decoding="async">
                    <figcaption><span>PROJECTS &amp; INFRASTRUCTURE</span><span>Perspective before action</span></figcaption>
                </figure>
            </div>
            <div class="image-principles">
                <div><span>A</span><b>Intelligence<br>before action</b></div>
                <div><span>B</span><b>Clarity through<br>complexity</b></div>
                <div><span>C</span><b>Confidence in<br>every decision</b></div>
                <div class="image-principle-promise"><span>OUR COMMITMENT</span><b>Precision in<br>Every Project.</b></div>
            </div>
        </section>

        <section class="image-services section-pad" id="services" aria-labelledby="services-title">
            <div class="section-index"><span>02</span><span class="index-rule"></span><span>OUR SERVICES</span></div>
            <div class="image-section-heading">
                <h2 id="services-title">Perspective for the<br><em>whole project lifecycle.</em></h2>
                <p>Five connected disciplines. One considered view of the decisions, risks and opportunities that shape project outcomes.</p>
            </div>
            <div class="image-service-grid">
                @foreach (config('beal.services') as $service)
                    <article class="image-service-card{{ $service['number'] === '05' ? ' image-service-card-featured' : '' }}">
                        <div class="image-service-photo"><img src="{{ asset($service['image']) }}" alt="{{ $service['image_alt'] }}" width="1400" height="933" loading="lazy" decoding="async"><span>{{ $service['number'] }}</span></div>
                        <div class="image-service-body">
                            <h3>{{ $service['title'] }}</h3>
                            <p>{{ $service['description'] }}</p>
                            <div class="image-service-subservices">@foreach ($service['subservices'] as $subservice){{ $subservice }}@if (! $loop->last) <i>·</i> @endif @endforeach</div>
                            <a class="image-service-link" href="{{ route('services.show', ['service' => $service['slug']]) }}" aria-label="Explore {{ $service['title'] }}">
                                Explore service <span aria-hidden="true">↗</span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="image-sectors section-pad" id="sectors" aria-labelledby="sectors-title">
            <div class="image-sectors-photo" aria-hidden="true"><img src="{{ asset('assets/home-image/urban-infrastructure.jpg') }}" alt="" width="1500" height="1000" loading="lazy" decoding="async"></div>
            <div class="image-sectors-shade" aria-hidden="true"></div>
            <div class="image-sectors-content">
                <div class="sectors-topline"><div class="section-index section-index-light"><span>03</span><span class="index-rule"></span><span>SECTORS</span></div><span class="sectors-aside">A connected view of<br>Africa's built environment.</span></div>
                <div class="image-section-heading image-sectors-heading"><h2 id="sectors-title">Where decisions<br><em>shape what comes next.</em></h2><p>Our perspective spans the sectors where projects, infrastructure and investment meet long-term public and commercial value.</p></div>
                <div class="image-sector-grid">
                    <a href="#contact"><span>01</span><b>Real estate &amp;<br>property development</b><i aria-hidden="true">↗</i></a>
                    <a href="#contact"><span>02</span><b>Infrastructure &amp;<br>transport</b><i aria-hidden="true">↗</i></a>
                    <a href="#contact"><span>03</span><b>Commercial &amp;<br>institutional development</b><i aria-hidden="true">↗</i></a>
                    <a href="#contact"><span>04</span><b>Industrial &amp;<br>manufacturing</b><i aria-hidden="true">↗</i></a>
                    <a href="#contact"><span>05</span><b>Energy &amp;<br>utilities</b><i aria-hidden="true">↗</i></a>
                    <a href="#contact"><span>06</span><b>Public sector &amp;<br>development programmes</b><i aria-hidden="true">↗</i></a>
                </div>
            </div>
        </section>

        <section class="image-insights section-pad" id="insights" aria-labelledby="insights-title">
            <div class="section-index"><span>04</span><span class="index-rule"></span><span>INSIGHTS &amp; INTELLIGENCE</span></div>
            <div class="image-insights-grid">
                <div class="image-insights-copy">
                    <p class="eyebrow eyebrow-navy"><span class="eyebrow-rule"></span> A PLATFORM FOR PERSPECTIVE</p>
                    <h2 id="insights-title">Insight is a<br><em>starting point.</em></h2>
                    <p>Ideas and analysis can make complexity easier to navigate. Our publishing platform is designed to share informed perspectives on the forces shaping projects and infrastructure.</p>
                    <a class="text-link text-link-navy" href="#contact">Explore our thinking <span aria-hidden="true">↗</span></a>
                </div>
                <figure class="image-insights-feature">
                    <img src="{{ asset('assets/home-image/nairobi-skyline.jpg') }}" alt="Panoramic view across Nairobi's skyline and green city canopy" width="2200" height="1237" loading="lazy" decoding="async">
                    <figcaption><span>INSIGHTS &amp; INTELLIGENCE</span><b>Research / Perspective / Analysis</b></figcaption>
                </figure>
            </div>
            <p class="image-insights-note"><span></span>New perspectives will appear here as BEAL publications are approved for release.</p>
        </section>

        <section class="image-experience section-pad" id="experience" aria-labelledby="experience-title">
            <div class="image-experience-copy">
                <div class="section-index"><span>05</span><span class="index-rule"></span><span>PROJECTS &amp; EXPERIENCE</span></div>
                <h2 id="experience-title">Experience, presented<br><em>with context.</em></h2>
                <p>A considered record of project exposure, sector knowledge and professional contribution belongs here. Each entry will make clear whether it reflects a BEAL engagement or prior professional experience.</p>
                <a class="text-link text-link-navy" href="#contact">Discuss experience <span aria-hidden="true">↗</span></a>
            </div>
            <figure class="image-experience-photo"><img src="{{ asset('assets/home-image/city-development.jpg') }}" alt="Illustrative city development scene with cranes above buildings" width="1400" height="933" loading="lazy" decoding="async"><figcaption>Precision in Every Project.</figcaption></figure>
        </section>

        <section class="image-contact" id="contact" aria-labelledby="contact-title">
            <img src="{{ asset('assets/home-image/urban-infrastructure.jpg') }}" alt="" width="1500" height="1000" loading="lazy" decoding="async">
            <div class="image-contact-shade" aria-hidden="true"></div>
            <div class="image-contact-content"><div class="section-index section-index-light"><span>06</span><span class="index-rule"></span><span>START A CONVERSATION</span></div><div class="image-contact-layout"><h2 id="contact-title">Better decisions<br>begin with <em>a conversation.</em></h2><div><p>For an enquiry about BEAL's advisory and infrastructure consulting capabilities, connect with the team.</p>@if (config('beal.contact_email'))<a class="button button-gold" href="mailto:{{ config('beal.contact_email') }}">Get in touch <span aria-hidden="true">↗</span></a>@else<span class="button button-gold" aria-disabled="true">Get in touch <span aria-hidden="true">↗</span></span><span class="contact-note">Contact details are being confirmed.</span>@endif</div></div><div class="image-contact-bottom"><span>Clarity in Projects.<br>Confidence in Decisions.</span><span>Precision in Every Project.</span></div></div>
        </section>
    </main>

    <footer class="site-footer image-footer">
        <div class="footer-main"><a class="footer-brand" href="#top"><img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting" width="2167" height="726"></a><div class="footer-statement">Independent thinking.<br><em>Informed decisions.</em></div><div class="footer-nav"><span>EXPLORE</span><a href="#about">About BEAL</a><a href="#services">Our Services</a><a href="#insights">Insights &amp; Intelligence</a></div><div class="footer-nav"><span>CONNECT</span><a href="#contact">Contact BEAL</a><span>Nairobi, Kenya</span></div></div>
        {{-- Photo credits are temporarily hidden; keep them here for when they are needed again.
        <div class="image-credit">Photography: <a href="https://unsplash.com/photos/a-cityscape-of-nairobi-kenya-on-a-sunny-day-IaJm3mq0F5o" target="_blank" rel="noopener noreferrer">imsogabriel stock</a>, <a href="https://unsplash.com/photos/modern-architectural-building-with-glass-facade-QLAlBlus3SI" target="_blank" rel="noopener noreferrer">Declan Sun</a>, <a href="https://unsplash.com/photos/someone-analyzes-financial-data-on-a-tablet-EL16ACtwLxg" target="_blank" rel="noopener noreferrer">Jakub Żerdzicki</a> and <a href="https://unsplash.com/photos/curving-elevated-highway-with-concrete-pillars-against-sky-hfauTxL68qw" target="_blank" rel="noopener noreferrer">Declan Sun</a>; city development image by <a href="https://unsplash.com/photos/construction-cranes-tower-over-a-city-skyline-GROWZ5llj9w" target="_blank" rel="noopener noreferrer">Luan Fonseca</a> on Unsplash.</div> --}}
        <div class="footer-legal"><span>© {{ date('Y') }} BEAL Consulting Ltd</span><span>Precision in Every Project.</span><a href="#top">Back to top ↑</a></div>
    </footer>
</body>
</html>
