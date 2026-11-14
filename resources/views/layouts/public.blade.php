<!doctype html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="offline-cacheable" content="public">
    <meta name="theme-color" content="#f4f3ef">
    <meta name="description" content="{{ $metaDescription ?? $settings?->meta_description ?? $settings?->short_bio ?? '' }}">
    <link rel="canonical" href="{{ request()->url() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <title>{{ $pageTitle ?? $settings?->site_name ?? 'Architecture Portfolio' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="site-body">
    @php
        $logoUrl = $settings?->publicImageUrl($settings->logo_path);
        $whatsapp = $settings?->whatsapp_number
            ? 'https://wa.me/'.preg_replace('/\D/', '', $settings->whatsapp_number).($settings->whatsapp_message ? '?text='.urlencode($settings->whatsapp_message) : '')
            : null;
    @endphp

    <header class="nav site-nav" x-data="{ menuOpen: false }" x-on:keydown.escape.window="menuOpen = false" x-on:click.outside="menuOpen = false">
        <a class="site-brand {{ $logoUrl ? 'has-logo' : 'has-name' }}" href="{{ route('home') }}" aria-label="{{ $settings?->site_name ?? 'Home' }}">
            @if ($logoUrl)
                <img class="site-logo" src="{{ $logoUrl }}" alt="{{ $settings?->site_name ?? 'Site logo' }}">
            @else
                <span class="wordmark">{{ $settings?->site_name ?? 'Architecture' }}</span>
            @endif
        </a>
        <button class="site-menu-toggle" type="button" x-bind:aria-label="menuOpen ? 'Close navigation menu' : 'Open navigation menu'" x-on:click="menuOpen = !menuOpen" x-bind:aria-expanded="menuOpen.toString()">☰</button>
        @if ($settings?->instagram_url || $settings?->facebook_url || $settings?->linkedin_url || $settings?->youtube_url || $settings?->pinterest_url)
            <div class="header-socials" aria-label="Social media">
                @include('layouts.partials.social-links')
            </div>
        @endif
        <nav class="site-menu" aria-label="Main navigation" x-bind:class="menuOpen ? 'is-open' : ''">
            <span class="site-menu-section-label" data-en="Pages" data-ar="الصفحات">Pages</span>
            <a @class(['is-current' => request()->routeIs('home')]) href="{{ route('home') }}" data-en="Home" data-ar="الرئيسية">Home</a>
            <a @class(['is-current' => request()->routeIs('projects.*')]) href="{{ route('projects.index') }}" data-en="Projects" data-ar="المشاريع">Projects</a>
            <a @class(['is-current' => request()->routeIs('about')]) href="{{ route('about') }}" data-en="About" data-ar="من نحن">About</a>
            @guest
                <a @class(['is-current' => request()->routeIs('login')]) href="{{ route('login') }}" data-en="Sign in" data-ar="تسجيل الدخول">Sign in</a>
                <a @class(['is-current' => request()->routeIs('register')]) href="{{ route('register') }}" data-en="Sign up" data-ar="إنشاء حساب">Sign up</a>
            @else
                <a @class(['is-current' => request()->routeIs('account.password')]) href="{{ route('account.password') }}" data-en="Account" data-ar="الحساب">Account</a>
                <form method="post" action="{{ route('logout') }}" class="site-logout-form">@csrf<button type="submit" data-en="Sign out" data-ar="تسجيل الخروج">Sign out</button></form>
            @endguest
            <span class="site-menu-section-label site-menu-preferences-label" data-en="Preferences" data-ar="التفضيلات">Preferences</span>
            <div class="site-menu-controls" aria-label="Site preferences">
                <button type="button" class="site-menu-option" data-language-toggle aria-label="Change language">العربية</button>
                <button type="button" class="site-menu-option" data-theme-toggle data-light-label="Dark mode" data-dark-label="Light mode" aria-label="Change color mode">Dark mode</button>
            </div>
        </nav>
    </header>

    <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
    <main>{{ $slot }}</main>

    @if ($whatsapp)
        <a class="whatsapp-fab" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" aria-label="Contact on WhatsApp">↗</a>
    @endif

    <footer class="site-footer">
        <div class="footer-socials" aria-label="Social media">
            <span class="socials-label" data-en="Follow the studio" data-ar="تابع الاستوديو">Follow the studio</span>
            @include('layouts.partials.social-links')
        </div>
        <span>{{ $settings?->site_name ?? 'Architecture Portfolio' }}</span>
        <span>© {{ now()->year }}</span>
        <a class="admin-login-link" href="{{ route('admin.login') }}">Administrator sign in</a>
    </footer>

    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
