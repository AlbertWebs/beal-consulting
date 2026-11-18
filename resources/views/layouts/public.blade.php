<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="theme-color" content="#0b1b31">
    <title>{{ $pageTitle }}</title><meta name="description" content="{{ $pageDescription }}"><meta name="robots" content="{{ $robots ?? ((($isPreview ?? false) || ! config('beal.indexing_enabled')) ? 'noindex,nofollow' : ($settings['seo.robots'] ?? 'index,follow')) }}">
    <link rel="canonical" href="{{ $canonicalUrl }}"><link rel="icon" type="image/png" href="{{ ($settings['branding.favicon'] ?? '') ?: asset('assets/favicon.png') }}"><link rel="apple-touch-icon" href="{{ asset('assets/beal-logo.png') }}">
    <meta property="og:type" content="{{ $ogType ?? 'website' }}"><meta property="og:title" content="{{ $pageTitle }}"><meta property="og:description" content="{{ $pageDescription }}"><meta property="og:url" content="{{ $canonicalUrl }}"><meta property="og:image" content="{{ ($settings['branding.social_sharing_image'] ?? '') ?: asset('assets/beal-horizontal-logo.png') }}"><meta name="twitter:card" content="summary_large_image">
    <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script type="application/ld+json">{!! json_encode(['@'.'context'=>'https://schema.org','@type'=>'Organization','name'=>$settings['general.legal_name'] ?? 'BEAL Consulting Ltd','url'=>rtrim((string)($settings['general.site_url'] ?? ''),'/'),'description'=>$settings['general.description'] ?? ''], JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!}</script>
    @yield('head')
    @vite(['resources/css/app.css','resources/js/app.js'])
    @if(preg_match('/^G-[A-Z0-9]+$/', (string)($settings['integrations.google_analytics_id'] ?? '')))<script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings['integrations.google_analytics_id'] }}"></script><script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments)}gtag('js',new Date());gtag('config','{{ $settings['integrations.google_analytics_id'] }}');</script>@endif
    @if(preg_match('/^GTM-[A-Z0-9]+$/', (string)($settings['seo.google_tag_manager_id'] ?? '')))<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','{{ $settings['seo.google_tag_manager_id'] }}');</script>@endif
</head>
<body class="image-home secondary-page" id="top">
    @if($isPreview ?? false)<div class="draft-preview-banner" role="status">Authenticated draft preview · not published</div>@endif
    <a class="skip-link" href="#main">Skip to content</a><x-site-header :settings="$settings" />
    <main id="main">@yield('content')</main>
    <x-site-footer :settings="$settings" /><x-enquiry-dialog :service-options="$serviceOptions ?? []" />
</body>
</html>
