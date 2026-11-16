<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1b31">
    <meta name="description" content="BEAL Consulting brings intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions.">
    <meta name="robots" content="noindex,follow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="BEAL Consulting | Clarity in Projects. Confidence in Decisions.">
    <meta property="og:description" content="Intelligence-led strategic advisory and infrastructure consultancy across East Africa and the wider African market.">
    <meta property="og:image" content="{{ asset('assets/beal-horizontal-logo.png') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/beal-logo.png') }}">
    <title>BEAL Consulting | Clarity in Projects. Confidence in Decisions.</title>
    <script type="application/ld+json">{!! $organizationJson !!}</script>
    @if (config('beal.google_analytics_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ urlencode(config('beal.google_analytics_id')) }}"></script>
        <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config',@json(config('beal.google_analytics_id')));</script>
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <div class="topline">
        <div class="topline-inner">
            <span class="topline-location"><span class="location-mark" aria-hidden="true"></span>Nairobi, Kenya <span class="topline-divider">/</span> Across Africa</span>
            <span class="topline-note">Independent thinking. Informed decisions.</span>
            <span class="topline-index">Strategic advisory <span>-</span> Infrastructure intelligence</span>
        </div>
    </div>
    <header class="site-header" data-header>
        <div class="header-inner">
            <a class="brand" href="#top" aria-label="BEAL Consulting home">
                <img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting - Precision in Every Project" width="2167" height="726">
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
        <section class="hero" id="top" aria-labelledby="hero-title">
            <div class="hero-grid" aria-hidden="true"></div>
            <div class="hero-content">
                <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span><span class="hero-brand-label">BEAL CONSULTING LTD</span><span class="eyebrow-divider">/</span><span class="hero-location-label">NAIROBI, KENYA</span></p>
                <h1 id="hero-title">Clarity in Projects.<br><em>Confidence in Decisions.</em></h1>
                <p class="hero-summary">We bring intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions.</p>
                <div class="hero-actions">
                    <a class="button button-gold" href="#services">Explore our expertise <span aria-hidden="true">↗</span></a>
                    <a class="text-link text-link-light" href="#about">Discover BEAL <span aria-hidden="true">↓</span></a>
                </div>
                <div class="hero-footnote"><span class="footnote-line"></span><span>Strategic perspective for a changing Africa</span></div>
            </div>
            <div class="hero-art" aria-hidden="true">
                <div class="art-coordinate coordinate-one">01 <span>/ 04</span></div>
                <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
                <div class="art-axis axis-one"></div><div class="art-axis axis-two"></div>
                <div class="art-node node-one"></div><div class="art-node node-two"></div><div class="art-node node-three"></div>
                <div class="art-structure">
                    <div class="structure-tower"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                    <div class="structure-mid"><i></i><i></i><i></i><i></i><i></i></div>
                    <div class="structure-low"><i></i><i></i><i></i><i></i></div>
                    <div class="structure-base"></div>
                </div>
                <div class="art-caption"><span>PROJECT INTELLIGENCE</span><b>Insight that moves<br>decisions forward.</b></div>
                <div class="art-coordinate coordinate-two">S 01° 17' <span>E 36° 49'</span></div>
            </div>
            <div class="hero-bottom"><span>Precision in every project.</span><span class="hero-bottom-line"></span><span>01 - 05</span></div>
        </section>

        <section class="intro-section section-pad" id="about" aria-labelledby="intro-title">
            <div class="section-index"><span>01</span><span class="index-rule"></span><span>THE BEAL PERSPECTIVE</span></div>
            <div class="intro-layout">
                <h2 id="intro-title">Complexity deserves <em>clear thinking.</em></h2>
                <div class="intro-copy">
                    <p class="lead-copy">We are an intelligence-led strategic advisory and infrastructure consultancy. We bring analytical insight and technical perspective to the decisions that shape projects.</p>
                    <p>We look beyond the immediate task to understand context, connect information and help decision-makers move forward with confidence.</p>
                    <a class="text-link text-link-navy" href="#services">How we work <span aria-hidden="true">↗</span></a>
                </div>
            </div>
            <div class="principle-strip">
                <div><span class="principle-mark">A</span><span>Intelligence<br>before action</span></div>
                <div><span class="principle-mark">B</span><span>Clarity through<br>complexity</span></div>
                <div><span class="principle-mark">C</span><span>Confidence in<br>every decision</span></div>
                <div class="principle-tagline"><span>OUR COMMITMENT</span><b>Precision in<br>Every Project.</b></div>
            </div>
        </section>

        <section class="services-section section-pad" id="services" aria-labelledby="services-title">
            <div class="section-index"><span>02</span><span class="index-rule"></span><span>OUR SERVICES</span></div>
            <div class="section-heading-row">
                <h2 id="services-title">Perspective for the <em>whole project lifecycle.</em></h2>
                <p>Five connected disciplines. One considered view of the decisions, risks and opportunities that shape project outcomes.</p>
            </div>
            <div class="service-list">
                <article class="service-item"><span class="service-number">01</span><div class="service-name"><h3>Strategic Intelligence <span>&amp; Advisory</span></h3><p>Research, analysis and decision support for complex opportunities.</p></div><div class="service-detail">Strategic research &amp; analysis <i>·</i> Feasibility &amp; development advisory <i>·</i> Investment &amp; project intelligence <i>·</i> Decision-support services</div><span class="service-arrow" aria-hidden="true">↗</span></article>
                <article class="service-item"><span class="service-number">02</span><div class="service-name"><h3>Project <span>&amp; Programme</span> Advisory</h3><p>Disciplined direction from planning through delivery.</p></div><div class="service-detail">Project &amp; programme management <i>·</i> Project planning &amp; controls <i>·</i> Monitoring &amp; reporting <i>·</i> Delivery strategy</div><span class="service-arrow" aria-hidden="true">↗</span></article>
                <article class="service-item"><span class="service-number">03</span><div class="service-name"><h3>Governance, Risk <span>&amp;</span> Performance</h3><p>Oversight that strengthens accountability and performance.</p></div><div class="service-detail">Project governance <i>·</i> Portfolio oversight <i>·</i> Risk management <i>·</i> Performance monitoring <i>·</i> Contract &amp; commercial advisory</div><span class="service-arrow" aria-hidden="true">↗</span></article>
                <article class="service-item"><span class="service-number">04</span><div class="service-name"><h3>Infrastructure <span>&amp;</span> Development Consultancy</h3><p>Technical and development insight for the built environment.</p></div><div class="service-detail">Infrastructure development advisory <i>·</i> Construction &amp; development consultancy <i>·</i> Technical coordination <i>·</i> Quality, health, safety &amp; environmental advisory <i>·</i> Sustainability considerations</div><span class="service-arrow" aria-hidden="true">↗</span></article>
                <article class="service-item"><span class="service-number">05</span><div class="service-name"><h3>Digital <span>&amp;</span> Analytical Solutions</h3><p>Useful intelligence, made visible and actionable.</p></div><div class="service-detail">Project performance analytics <i>·</i> Data-driven reporting <i>·</i> Digital project controls <i>·</i> Management dashboards <i>·</i> Decision-support tools</div><span class="service-arrow" aria-hidden="true">↗</span></article>
            </div>
        </section>

        <section class="sectors-section section-pad" id="sectors" aria-labelledby="sectors-title">
            <div class="sectors-topline"><div class="section-index section-index-light"><span>03</span><span class="index-rule"></span><span>SECTORS</span></div><span class="sectors-aside">A connected view of<br>Africa's built environment.</span></div>
            <div class="sectors-heading"><h2 id="sectors-title">Where decisions<br><em>shape what comes next.</em></h2><p>Our perspective spans the sectors where projects, infrastructure and investment meet long-term public and commercial value.</p></div>
            <div class="sector-grid">
                <div class="sector-cell"><span>01</span><b>Real estate <i>&amp;</i><br>property development</b><span class="sector-symbol">↗</span></div>
                <div class="sector-cell"><span>02</span><b>Infrastructure<br><i>&amp;</i> transport</b><span class="sector-symbol">↗</span></div>
                <div class="sector-cell"><span>03</span><b>Commercial <i>&amp;</i><br>institutional development</b><span class="sector-symbol">↗</span></div>
                <div class="sector-cell"><span>04</span><b>Industrial <i>&amp;</i><br>manufacturing</b><span class="sector-symbol">↗</span></div>
                <div class="sector-cell"><span>05</span><b>Energy <i>&amp;</i><br>utilities</b><span class="sector-symbol">↗</span></div>
                <div class="sector-cell"><span>06</span><b>Public sector <i>&amp;</i><br>development programmes</b><span class="sector-symbol">↗</span></div>
            </div>
        </section>

        <section class="insights-section section-pad" id="insights" aria-labelledby="insights-title">
            <div class="section-index"><span>04</span><span class="index-rule"></span><span>INSIGHTS &amp; INTELLIGENCE</span></div>
            <div class="insights-layout">
                <div class="insights-heading"><p class="eyebrow eyebrow-navy"><span class="eyebrow-rule"></span> A PLATFORM FOR PERSPECTIVE</p><h2 id="insights-title">Insight is a<br><em>starting point.</em></h2><p>Ideas and analysis can make complexity easier to navigate. Our publishing platform is designed to share informed perspectives on the forces shaping projects and infrastructure.</p><a class="text-link text-link-navy" href="#contact">Explore our thinking <span aria-hidden="true">↗</span></a></div>
                <div class="editorial-card" aria-label="Insights publishing platform preview">
                    <div class="editorial-top"><span>INSIGHTS<br>&amp; INTELLIGENCE</span><span class="editorial-emblem" aria-hidden="true">B<span>.</span></span></div>
                    <div class="editorial-lines" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
                    <div class="editorial-bottom"><span>RESEARCH <b>/</b> PERSPECTIVE <b>/</b> ANALYSIS</span><span class="editorial-date">BEAL<br>CONSULTING</span></div>
                </div>
            </div>
            <div class="insights-note"><span class="note-dot"></span><span>New perspectives will appear here as BEAL publications are approved for release.</span></div>
        </section>

        <section class="experience-section section-pad" id="experience" aria-labelledby="experience-title">
            <div class="experience-copy"><div class="section-index"><span>05</span><span class="index-rule"></span><span>PROJECTS &amp; EXPERIENCE</span></div><h2 id="experience-title">Experience, presented<br><em>with context.</em></h2><p>A considered record of project exposure, sector knowledge and professional contribution belongs here. Each entry will make clear whether it reflects a BEAL engagement or prior professional experience.</p><a class="text-link text-link-navy" href="#contact">Discuss experience <span aria-hidden="true">↗</span></a></div>
                <div class="experience-visual" aria-hidden="true"><div class="experience-arch arch-one"></div><div class="experience-arch arch-two"></div><div class="experience-grid"></div><div class="experience-caption"><span>PROJECTS &amp; EXPERIENCE</span><b>Grounded in<br>professional context.</b></div></div>
        </section>

        <section class="contact-section" id="contact" aria-labelledby="contact-title">
            <div class="contact-inner"><div class="section-index section-index-light"><span>06</span><span class="index-rule"></span><span>START A CONVERSATION</span></div><div class="contact-layout"><h2 id="contact-title">Better decisions<br>begin with <em>a conversation.</em></h2><div><p>Tell us about your project or question, and our team will be in touch.</p><button class="button button-gold" type="button" data-enquiry-open>Get in touch <span aria-hidden="true">↗</span></button></div></div><div class="contact-bottom"><img src="{{ asset('assets/beal-logo.png') }}" alt="" width="1276" height="1233"><span>Clarity in Projects.<br>Confidence in Decisions.</span><span class="contact-bottom-tag">Precision in Every Project.</span></div></div>
        </section>
    </main>

    <footer class="site-footer"><div class="footer-main"><a class="footer-brand" href="#top"><img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting" width="2167" height="726"></a><div class="footer-statement">Independent thinking.<br><em>Informed decisions.</em></div><div class="footer-nav"><span>EXPLORE</span><a href="#about">About BEAL</a><a href="#services">Our Services</a><a href="#insights">Insights &amp; Intelligence</a></div><div class="footer-nav"><span>CONNECT</span><a href="#contact">Contact BEAL</a><span>Nairobi, Kenya</span></div></div><div class="footer-legal"><span>© {{ date('Y') }} BEAL Consulting Ltd</span><span>Precision in Every Project.</span><a href="#top">Back to top ↑</a></div></footer>
    <x-enquiry-dialog />
</body>
</html>
