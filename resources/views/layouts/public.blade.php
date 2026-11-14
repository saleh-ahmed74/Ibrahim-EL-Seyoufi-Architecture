<!doctype html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.favicon')
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
        <a class="whatsapp-fab" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" aria-label="Contact on WhatsApp" title="WhatsApp">
            <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                <path d="M16 3.2a12.65 12.65 0 0 0-10.8 19.2L3.5 28.5l6.3-1.65A12.65 12.65 0 1 0 16 3.2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 9.8c-.35-.78-.73-.8-1.08-.8h-.92c-.32 0-.84.12-1.28.6-.44.47-1.68 1.64-1.68 4s1.72 4.63 1.96 4.95c.24.31 3.33 5.32 8.22 7.24 4.05 1.6 4.88 1.28 5.76 1.2.88-.08 2.84-1.16 3.24-2.28.4-1.12.4-2.08.28-2.28-.12-.2-.44-.32-.92-.56s-2.84-1.4-3.28-1.56c-.44-.16-.76-.24-1.08.24-.32.48-1.24 1.56-1.52 1.88-.28.32-.56.36-1.04.12-.48-.24-2.02-.75-3.85-2.38-1.42-1.27-2.38-2.84-2.66-3.32-.28-.48-.03-.74.21-.98.21-.21.48-.56.72-.84.24-.28.32-.48.48-.8.16-.32.08-.6-.04-.84-.12-.24-1.05-2.6-1.45-3.54Z" fill="currentColor" transform="translate(4 0) scale(.76)"/>
            </svg>
        </a>
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
