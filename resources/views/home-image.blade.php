<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b1b31">
    <meta name="description" content="{{ $settings['seo.description'] ?? 'BEAL Consulting brings intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions.' }}">
    <meta name="robots" content="{{ ($isPreview ?? false) || ! config('beal.indexing_enabled') ? 'noindex,nofollow' : ($settings['seo.robots'] ?? 'index,follow') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $settings['seo.title'] ?? 'BEAL Consulting | Clarity in Projects. Confidence in Decisions.' }}">
    <meta property="og:description" content="{{ $settings['seo.description'] ?? 'Intelligence-led strategic advisory and infrastructure consultancy across East Africa and the wider African market.' }}">
    <meta property="og:image" content="{{ ($settings['branding.social_sharing_image'] ?? '') ?: $sectionImages['hero'] }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/png" href="{{ ($settings['branding.favicon'] ?? '') ?: asset('assets/favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/beal-logo.png') }}">
    <title>{{ $settings['seo.title'] ?? 'BEAL Consulting | Clarity in Projects. Confidence in Decisions.' }}</title>
    <style>:root{--navy:{{ $settings['branding.primary_color'] ?? '#0b1b31' }};--gold:{{ $settings['branding.accent_color'] ?? '#c8a15d' }}}</style>
    <script type="application/ld+json">{!! $organizationJson !!}</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="image-home parallax-home">
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
            <a class="brand" href="#top" aria-label="BEAL Consulting home">
                <img src="{{ ($settings['branding.horizontal_logo'] ?? '') ?: asset('assets/beal-horizontal-logo.png') }}" alt="{{ $settings['general.site_name'] ?? 'BEAL Consulting' }}" width="1600" height="600">
            </a>
            <button class="menu-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="primary-navigation" data-menu-toggle>
                <span></span><span></span><span></span>
            </button>
            <nav class="navigation" id="primary-navigation" aria-label="Main navigation" data-navigation>
                @foreach(($settings['header.navigation'] ?? []) as $item) @if($item['enabled'] ?? true)<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@endif @endforeach
            </nav>
            <a class="button button-small button-dark header-cta" href="{{ $settings['header.cta_url'] ?? '#contact' }}" data-contact-cta>{{ $settings['header.cta_label'] ?? 'Start a conversation' }} <span aria-hidden="true">↗</span></a>
        </div>
    </header>

    <main id="main">
        @if($visible['hero'] ?? true)<section class="image-hero" id="top" style="order:{{ $sectionOrders['hero'] ?? 10 }}" aria-labelledby="hero-title">
            <img class="image-hero-photo" src="{{ $sectionImages['hero'] }}" alt="{{ $content['hero']['image_alt'] ?? '' }}" width="2200" height="1237" fetchpriority="high" decoding="async">
            <div class="image-hero-shade" aria-hidden="true"></div>
            <div class="image-hero-grid" aria-hidden="true"></div>
            <div class="image-hero-inner">
                <div class="image-hero-copy">
                    <p class="eyebrow eyebrow-light"><span class="eyebrow-rule"></span><span>{{ $content['hero']['eyebrow'] ?? 'BEAL CONSULTING LTD' }}</span><span class="eyebrow-divider">/</span><span>{{ $content['hero']['location'] ?? 'NAIROBI, KENYA' }}</span></p>
                    <h1 id="hero-title">{{ $content['hero']['title'] ?? 'Clarity in Projects.' }}<br><em>{{ $content['hero']['title_accent'] ?? 'Confidence in Decisions.' }}</em></h1>
                    <div class="image-hero-summary">{!! $content['hero']['summary'] ?? '<p>We bring intelligence, strategic insight and professional expertise to complex projects and infrastructure decisions.</p>' !!}</div>
                    <div class="hero-actions">
                        <a class="button button-gold" href="{{ $content['hero']['primary_button_url'] ?? '#services' }}">{{ $content['hero']['primary_button_label'] ?? 'Explore our expertise' }} <span aria-hidden="true">↗</span></a>
                        <a class="text-link text-link-light" href="{{ $content['hero']['secondary_button_url'] ?? '#about' }}">{{ $content['hero']['secondary_button_label'] ?? 'Discover BEAL' }} <span aria-hidden="true">↓</span></a>
                    </div>
                </div>
                <div class="image-hero-note"><span>{{ $content['hero']['note_number'] ?? '01 / 05' }}</span><span>{{ $content['hero']['note'] ?? 'Strategic perspective for a changing Africa' }}</span></div>
            </div>
            <div class="image-hero-caption"><span>{{ $content['hero']['location'] ?? 'NAIROBI, KENYA' }}</span><span>{{ $content['hero']['caption'] ?? ($settings['general.tagline'] ?? 'Independent thinking. Informed decisions.') }}</span></div>
        </section>@endif

        @if($visible['about'] ?? true)<section class="image-intro section-pad" id="about" style="order:{{ $sectionOrders['about'] ?? 20 }}" aria-labelledby="intro-title">
            <div class="section-index"><span>01</span><span class="index-rule"></span><span>{{ config('cms.sections.about.label') }}</span></div>
            <div class="image-intro-grid">
                <div class="image-intro-copy">
                    @php($aboutHeading = (string) ($content['about']['heading'] ?? 'Complexity deserves clear thinking.'))
                    @php($aboutHeadingAccent = 'clear thinking.')
                    @if (str_ends_with($aboutHeading, $aboutHeadingAccent))
                        <h2 id="intro-title">{{ trim(substr($aboutHeading, 0, -strlen($aboutHeadingAccent))) }} <em>{{ $aboutHeadingAccent }}</em></h2>
                    @else
                        <h2 id="intro-title">{{ $aboutHeading }}</h2>
                    @endif
                    <div class="lead-copy">{!! $content['about']['lead'] ?? '<p>We are an intelligence-led strategic advisory and infrastructure consultancy. We bring analytical insight and technical perspective to the decisions that shape projects.</p>' !!}</div>
                    <div>{!! $content['about']['body'] ?? '<p>We look beyond the immediate task to understand context, connect information and help decision-makers move forward with confidence.</p>' !!}</div>
                    <a class="text-link text-link-navy" href="#services">How we work <span aria-hidden="true">↗</span></a>
                    <a class="text-link text-link-navy" href="{{ route('pages.about') }}">More about BEAL <span aria-hidden="true">↗</span></a>
                </div>
                <figure class="image-intro-photo">
                    <img src="{{ $sectionImages['about'] }}" alt="{{ $content['about']['image_alt'] ?? '' }}" width="1500" height="1000" loading="lazy" decoding="async">
                    <figcaption><span>PROJECTS &amp; INFRASTRUCTURE</span><span>{{ $content['about']['image_caption'] ?? 'Perspective before action' }}</span></figcaption>
                </figure>
            </div>
            <div class="image-principles">
                @foreach($content['about']['principles'] ?? [] as $principle)<div><span>{{ $principle['letter'] ?? '' }}</span><b>{{ $principle['text'] ?? '' }}</b></div>@endforeach
                <div class="image-principle-promise"><span>{{ $content['about']['commitment_label'] ?? 'OUR COMMITMENT' }}</span><b>{{ $content['about']['commitment'] ?? 'Precision in Every Project.' }}</b></div>
            </div>
        </section>@endif

        @if($visible['services'] ?? true)<section class="image-services section-pad" id="services" style="order:{{ $sectionOrders['services'] ?? 30 }}" aria-labelledby="services-title">
            <div class="section-index"><span>02</span><span class="index-rule"></span><span>OUR SERVICES</span></div>
            <div class="image-section-heading">
                <h2 id="services-title">{{ $content['services']['heading'] ?? 'Perspective for the' }} <em>{{ $content['services']['heading_accent'] ?? 'whole project lifecycle.' }}</em></h2>
                <div>{!! $content['services']['description'] ?? '<p>Five connected disciplines. One considered view of the decisions, risks and opportunities that shape project outcomes.</p>' !!}</div>
            </div>
            <div class="image-service-grid">
                @foreach ($services as $service)
                    <article class="image-service-card{{ $service['number'] === '05' ? ' image-service-card-featured' : '' }}{{ $service['slug'] === 'governance-risk-performance' ? ' image-service-card-scorecard' : '' }}">
                        <div class="image-service-photo"><img src="{{ $service['image'] }}" alt="{{ $service['image_alt'] }}" width="1400" height="933" loading="lazy" decoding="async"><span>{{ $service['number'] }}</span></div>
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
        </section>@endif

        <section class="image-scorecard section-pad" id="project-health-scorecard" style="order:35" aria-labelledby="scorecard-title">
            <div class="section-index"><span>03</span><span class="index-rule"></span><span>PROJECT HEALTH ASSESSMENT</span></div>
            <div class="image-scorecard-heading">
                <div>
                    <p class="eyebrow eyebrow-navy"><span class="eyebrow-rule"></span>INDEPENDENT PROJECT OVERSIGHT</p>
                    <h2 id="scorecard-title">The BEAL Project Health<br><em>Scorecard</em></h2>
                    <p>We provide an independent view of project performance and risk. Our assessment brings the information decision-makers need into one clear picture, showing where performance is strong, pressure is building and action is needed.</p>
                </div>
                <a class="button button-gold" href="{{ route('services.show', ['service' => 'governance-risk-performance']) }}#service-scorecard">Explore the scorecard <span aria-hidden="true">↗</span></a>
            </div>
            <x-scorecard-assessment-grid />
            <div class="image-scorecard-outcomes">
                <div><span>01</span><p>See an overall project health view alongside ratings for each assessment area.</p></div>
                <div><span>02</span><p>Bring emerging risks, delays, cost concerns and decisions into focus.</p></div>
                <div><span>03</span><p>Translate findings into recommended actions, responsible parties and target dates.</p></div>
            </div>
        </section>

        @if($visible['sectors'] ?? true)<section class="image-sectors section-pad" id="sectors" style="order:{{ $sectionOrders['sectors'] ?? 40 }}" aria-labelledby="sectors-title">
            <div class="image-sectors-photo" aria-hidden="true"><img src="{{ $sectionImages['sectors'] }}" alt="" width="1500" height="1000" loading="lazy" decoding="async"></div>
            <div class="image-sectors-shade" aria-hidden="true"></div>
            <div class="image-sectors-content">
                <div class="sectors-topline"><div class="section-index section-index-light"><span>03</span><span class="index-rule"></span><span>{{ $content['sectors']['eyebrow'] ?? 'SECTORS' }}</span></div><span class="sectors-aside">{{ $content['sectors']['connected_label'] ?? 'A connected view of Africa’s built environment.' }}</span></div>
                <div class="image-section-heading image-sectors-heading"><h2 id="sectors-title">{{ $content['sectors']['heading'] ?? 'Where decisions' }}<br><em>{{ $content['sectors']['heading_accent'] ?? 'shape what comes next.' }}</em></h2><div>{!! $content['sectors']['description'] ?? '<p>Our perspective spans the sectors where projects, infrastructure and investment meet long-term public and commercial value.</p>' !!}</div></div>
                <div class="image-sector-grid">
                    @foreach($content['sectors']['items'] ?? [] as $sectorIndex=>$sector)@php($sectorEntry=$sectorEntries->get($sectorIndex))<a href="{{ !empty($sector['url']) && $sector['url'] !== '#contact' ? $sector['url'] : ($sectorEntry ? route('sectors.show',$sectorEntry->slug) : route('pages.contact')) }}"><span>{{ $sector['number'] ?? '' }}</span><b>{{ $sector['title'] ?? '' }}</b><i aria-hidden="true">↗</i></a>@endforeach
                </div>
            </div>
        </section>@endif

        @if($visible['insights'] ?? true)<section class="image-insights section-pad" id="insights" style="order:{{ $sectionOrders['insights'] ?? 50 }}" aria-labelledby="insights-title">
            <div class="section-index"><span>04</span><span class="index-rule"></span><span>INSIGHTS &amp; INTELLIGENCE</span></div>
            <div class="image-insights-grid">
                <div class="image-insights-copy">
                    <p class="eyebrow eyebrow-navy"><span class="eyebrow-rule"></span> {{ $content['insights']['platform_label'] ?? 'A PLATFORM FOR PERSPECTIVE' }}</p>
                    <h2 id="insights-title">{{ $content['insights']['heading'] ?? 'Insight is a' }}<br><em>{{ $content['insights']['heading_accent'] ?? 'starting point.' }}</em></h2>
                    <div>{!! $content['insights']['description'] ?? '<p>Ideas and analysis can make complexity easier to navigate. Our publishing platform is designed to share informed perspectives on the forces shaping projects and infrastructure.</p>' !!}</div>
                    <a class="text-link text-link-navy" href="{{ ($content['insights']['button_url'] ?? '#contact') === '#contact' ? route('pages.insights') : $content['insights']['button_url'] }}">{{ $content['insights']['button_label'] ?? 'Explore our thinking' }} <span aria-hidden="true">↗</span></a>
                </div>
                <figure class="image-insights-feature">
                    <img src="{{ $sectionImages['insights'] }}" alt="{{ $content['insights']['image_alt'] ?? '' }}" width="2200" height="1237" loading="lazy" decoding="async">
                    <figcaption><span>INSIGHTS &amp; INTELLIGENCE</span><b>{{ $content['insights']['image_caption'] ?? 'Research / Perspective / Analysis' }}</b></figcaption>
                </figure>
            </div>
            <p class="image-insights-note"><span></span>{{ $content['insights']['note'] ?? '' }}</p>
        </section>@endif

        @if($visible['experience'] ?? true)<section class="image-experience section-pad" id="experience" style="order:{{ $sectionOrders['experience'] ?? 60 }}" aria-labelledby="experience-title">
            <div class="image-experience-copy">
                <div class="section-index"><span>05</span><span class="index-rule"></span><span>PROJECTS &amp; EXPERIENCE</span></div>
                <h2 id="experience-title">{{ $content['experience']['heading'] ?? 'Experience, presented' }}<br><em>{{ $content['experience']['heading_accent'] ?? 'with context.' }}</em></h2>
                <div>{!! $content['experience']['description'] ?? '<p>A considered record of project exposure, sector knowledge and professional contribution belongs here. Each entry will make clear whether it reflects a BEAL engagement or prior professional experience.</p>' !!}</div>
                <a class="text-link text-link-navy" href="{{ ($content['experience']['button_url'] ?? '#contact') === '#contact' ? route('pages.experience') : $content['experience']['button_url'] }}">{{ $content['experience']['button_label'] ?? 'Discuss experience' }} <span aria-hidden="true">↗</span></a>
            </div>
            <figure class="image-experience-photo"><img src="{{ $sectionImages['experience'] }}" alt="{{ $content['experience']['image_alt'] ?? '' }}" width="1400" height="933" loading="lazy" decoding="async"><figcaption>{{ $content['experience']['image_caption'] ?? 'Precision in Every Project.' }}</figcaption></figure>
        </section>@endif

        @if($visible['contact'] ?? true)<section class="image-contact" id="contact" style="order:{{ $sectionOrders['contact'] ?? 70 }}" aria-labelledby="contact-title">
            <img src="{{ $sectionImages['contact'] }}" alt="" width="1500" height="1000" loading="lazy" decoding="async">
            <div class="image-contact-shade" aria-hidden="true"></div>
            <div class="image-contact-content"><div class="section-index section-index-light"><span>06</span><span class="index-rule"></span><span>{{ config('cms.sections.contact.label') }}</span></div><div class="image-contact-layout"><h2 id="contact-title">{{ $content['contact']['heading'] ?? 'Better decisions' }}<br>begin with <em>{{ $content['contact']['heading_accent'] ?? 'a conversation.' }}</em></h2><div>{!! $content['contact']['description'] ?? '<p>Tell us about your project or question, and our team will be in touch.</p>' !!}<button class="button button-gold" type="button" data-enquiry-open>{{ $content['contact']['button_label'] ?? 'Get in touch' }} <span aria-hidden="true">↗</span></button></div></div><div class="image-contact-bottom"><span>{{ $settings['general.tagline'] ?? 'Clarity in Projects. Confidence in Decisions.' }}</span><span>{{ $content['about']['commitment'] ?? 'Precision in Every Project.' }}</span></div></div>
        </section>@endif
    </main>

    <footer class="site-footer image-footer">
        @php($publicEmail = $settings['contact.email'] ?? $settings['general.public_email'] ?? '')
        @php($publicPhone = $settings['contact.phone'] ?? $settings['general.phone'] ?? '')
        @php($publicWhatsapp = $settings['contact.whatsapp'] ?? '')
        @php($publicLocation = $settings['contact.location'] ?? $settings['general.address'] ?? 'Nairobi, Kenya')
        @php($publicMapLink = $settings['contact.maps_url'] ?? $settings['general.maps_url'] ?? '')
        @php($publicHours = $settings['contact.business_hours'] ?? $settings['general.business_hours'] ?? '')
        <div class="footer-main"><a class="footer-brand" href="#top"><img src="{{ ($settings['branding.horizontal_logo'] ?? '') ?: asset('assets/beal-horizontal-logo.png') }}" alt="{{ $settings['general.site_name'] ?? 'BEAL Consulting' }}" width="1600" height="600"></a><div class="footer-statement">{{ $settings['footer.statement'] ?? 'Independent thinking. Informed decisions.' }}</div><div class="footer-nav"><span>{{ $settings['footer.explore_label'] ?? 'EXPLORE' }}</span>@foreach(($settings['footer.navigation'] ?? []) as $item) @if($item['enabled'] ?? true)<a href="{{ $item['url'] }}">{{ $item['label'] }}</a>@endif @endforeach</div><div class="footer-nav"><span>{{ $settings['footer.contact_label'] ?? 'CONNECT' }}</span><a href="{{ filled($publicEmail) ? 'mailto:'.$publicEmail : '#contact' }}">{{ $publicEmail ?: 'Contact BEAL' }}</a>@if(filled($publicPhone))<a href="tel:{{ preg_replace('/[^+0-9]/', '', $publicPhone) }}">{{ $publicPhone }}</a>@endif @if(filled($publicWhatsapp))<a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $publicWhatsapp) }}" target="_blank" rel="noopener noreferrer">WhatsApp BEAL</a>@endif @if(filled($publicLocation))<span>{{ $publicLocation }}</span>@endif @if(filled($publicMapLink))<a href="{{ $publicMapLink }}" rel="noopener noreferrer" target="_blank">View location <span aria-hidden="true">↗</span></a>@endif @if(filled($publicHours))<span>{{ $publicHours }}</span>@endif @foreach(($settings['footer.social_links'] ?? []) as $social) @if(($social['enabled'] ?? true) && filled($social['url'] ?? ''))<a href="{{ $social['url'] }}" rel="noopener noreferrer" target="_blank">{{ $social['label'] ?? 'Social' }}</a>@endif @endforeach</div></div>
        {{-- Photo credits are temporarily hidden; keep them here for when they are needed again.
        <div class="image-credit">Photography: <a href="https://unsplash.com/photos/a-cityscape-of-nairobi-kenya-on-a-sunny-day-IaJm3mq0F5o" target="_blank" rel="noopener noreferrer">imsogabriel stock</a>, <a href="https://unsplash.com/photos/modern-architectural-building-with-glass-facade-QLAlBlus3SI" target="_blank" rel="noopener noreferrer">Declan Sun</a>, <a href="https://unsplash.com/photos/someone-analyzes-financial-data-on-a-tablet-EL16ACtwLxg" target="_blank" rel="noopener noreferrer">Jakub Żerdzicki</a> and <a href="https://unsplash.com/photos/curving-elevated-highway-with-concrete-pillars-against-sky-hfauTxL68qw" target="_blank" rel="noopener noreferrer">Declan Sun</a>; city development image by <a href="https://unsplash.com/photos/construction-cranes-tower-over-a-city-skyline-GROWZ5llj9w" target="_blank" rel="noopener noreferrer">Luan Fonseca</a> on Unsplash.</div> --}}
        <div class="footer-legal"><span>{{ str_replace(':year', date('Y'), $settings['general.copyright'] ?? '© :year BEAL Consulting Ltd') }}</span><span>{{ $content['about']['commitment'] ?? 'Precision in Every Project.' }}</span><a href="#top">Back to top ↑</a></div>
    </footer>
    <x-enquiry-dialog :service-options="$serviceOptions ?? $services ?? []" />
</body>
</html>
