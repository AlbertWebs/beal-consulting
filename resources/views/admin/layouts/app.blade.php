<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>{{ $title ?? 'Dashboard' }} · BEAL CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cms-shell">
    <aside class="cms-sidebar" data-cms-sidebar>
        <a class="cms-brand" href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/beal-horizontal-logo.png') }}" alt="BEAL Consulting CMS"></a>
        <nav class="cms-nav" aria-label="CMS navigation">
            <div class="cms-nav-label">OVERVIEW</div>
            <a href="{{ route('admin.dashboard') }}" @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>◫ <span>Dashboard</span></a>
            @if(auth()->user()->canManageCms('pages'))
                <div class="cms-nav-label">CONTENT</div>
                <a href="{{ route('admin.pages.index') }}" @if(request()->routeIs('admin.pages.*')) aria-current="page" @endif>▤ <span>Pages</span></a>
            @endif
            @if(auth()->user()->canManageCms('services'))<a href="{{ route('admin.services.index') }}" @if(request()->routeIs('admin.services.*')) aria-current="page" @endif>⌂ <span>Services</span></a>@endif
            @if(auth()->user()->canManageCms('content'))
                <a href="{{ route('admin.content.index', ['type' => 'sector']) }}" @if(request()->route('type') === 'sector') aria-current="page" @endif>◈ <span>Sectors</span></a>
                <a href="{{ route('admin.content.index', ['type' => 'insight']) }}" @if(request()->routeIs('admin.content.*') && request()->route('type') === 'insight') aria-current="page" @endif>▧ <span>Insights</span></a>
                <a href="{{ route('admin.content.index', ['type' => 'project']) }}" @if(request()->routeIs('admin.content.*') && request()->route('type') === 'project') aria-current="page" @endif>⌂ <span>Experience</span></a>
            @endif
            @if(auth()->user()->canManageCms('media'))
                <div class="cms-nav-label">MEDIA</div><a href="{{ route('admin.media.index') }}" @if(request()->routeIs('admin.media.*')) aria-current="page" @endif>▧ <span>Media library</span></a>
            @endif
            @if(auth()->user()->canManageCms('submissions'))<div class="cms-nav-label">COMMUNICATION</div><a href="{{ route('admin.submissions.index') }}" @if(request()->routeIs('admin.submissions.*')) aria-current="page" @endif>✉ <span>Enquiries</span></a>@endif
            @if(auth()->user()->canManageCms('settings'))<div class="cms-nav-label">WEBSITE</div><a href="{{ route('admin.settings.edit') }}" @if(request()->routeIs('admin.settings.*')) aria-current="page" @endif>⚙ <span>Settings</span></a>@endif
            @if(auth()->user()->canManageCms('activity'))<a href="{{ route('admin.activity.index') }}" @if(request()->routeIs('admin.activity.*')) aria-current="page" @endif>◷ <span>Activity log</span></a>@endif
            @if(auth()->user()->canManageCms('users'))<a href="{{ route('admin.users.index') }}" @if(request()->routeIs('admin.users.*')) aria-current="page" @endif>♙ <span>Users &amp; roles</span></a>@endif
        </nav>
        <div style="border-top:1px solid #ffffff20;padding:14px 8px;color:#c1cad5"><strong>{{ auth()->user()->name }}</strong><div class="cms-muted" style="color:#95a4b7;font-size:12px">{{ auth()->user()->role }}</div><form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="cms-button" style="margin-top:10px;background:#ffffff15">Sign out</button></form></div>
    </aside>
    <div class="cms-main">
        <header class="cms-topbar"><div class="cms-actions"><button class="cms-button cms-mobile-toggle" type="button" data-cms-menu aria-label="Toggle menu">☰</button><div><div style="font-weight:600">{{ $title ?? 'Dashboard' }}</div><div class="cms-muted" style="font-size:12px">BEAL Consulting / {{ $title ?? 'Dashboard' }}</div></div></div><div class="cms-actions"><form action="{{ route('admin.search') }}" method="get"><input class="cms-input" name="q" value="{{ request('q') }}" placeholder="Search content…" aria-label="Search CMS" style="width:min(260px,35vw)"></form><a class="cms-button" href="{{ route('home') }}" target="_blank" rel="noopener">View website ↗</a></div></header>
        <main class="cms-workspace">
            @if(session('success'))<div class="cms-banner" data-cms-message role="status">✓ {{ session('success') }}</div>@endif
            @if($errors->any())<div class="cms-banner cms-banner-error" data-cms-message role="alert">{{ $errors->first() }}</div>@endif
            @yield('content')
        </main>
    </div>
    <div class="cms-toast" role="status" aria-live="polite" hidden><span data-cms-toast-message></span><button type="button" data-cms-toast-close aria-label="Dismiss notification">×</button></div>
    <script>
        document.querySelector('[data-cms-menu]')?.addEventListener('click',()=>document.querySelector('[data-cms-sidebar]')?.classList.toggle('is-open'));
        const cmsMessage=document.querySelector('[data-cms-message]');const cmsToast=document.querySelector('.cms-toast');if(cmsMessage&&cmsToast){cmsToast.querySelector('[data-cms-toast-message]').textContent=cmsMessage.textContent.trim();cmsToast.hidden=false;window.setTimeout(()=>{cmsToast.hidden=true},5000)}cmsToast?.querySelector('[data-cms-toast-close]')?.addEventListener('click',()=>{cmsToast.hidden=true});
    </script>
    @stack('scripts')
</body>
</html>
