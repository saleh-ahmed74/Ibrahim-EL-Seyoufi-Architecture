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
            @foreach (['instagram' => $settings?->instagram_url, 'facebook' => $settings?->facebook_url, 'linkedin' => $settings?->linkedin_url, 'youtube' => $settings?->youtube_url, 'pinterest' => $settings?->pinterest_url] as $network => $url)
                @if ($url)
                    <a class="social-link social-{{ $network }}" href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($network) }}" title="{{ ucfirst($network) }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            @switch($network)
                                @case('instagram')<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="18" cy="6" r=".8" fill="currentColor" stroke="none"/>@break
                                @case('facebook')<path d="M14 8h3V4h-3a5 5 0 0 0-5 5v3H6v4h3v4h4v-4h3l1-4h-4V9a1 1 0 0 1 1-1Z" fill="currentColor" stroke="none"/>@break
                                @case('linkedin')<path d="M5 8.5A2.25 2.25 0 1 0 5 4a2.25 2.25 0 0 0 0 4.5ZM3 10h4v11H3zm6 0h4v1.6c.7-1.1 1.8-1.9 3.6-1.9 3.2 0 4.4 2 4.4 5.1V21h-4v-5.5c0-1.5-.4-2.5-1.8-2.5s-2.2 1-2.2 2.5V21H9z" fill="currentColor" stroke="none"/>@break
                                @case('youtube')<path d="M22 8.2a3 3 0 0 0-2.1-2.1C18 5.6 12 5.6 12 5.6s-6 0-7.9.5A3 3 0 0 0 2 8.2 31 31 0 0 0 1.5 12a31 31 0 0 0 .5 3.8 3 3 0 0 0 2.1 2.1c1.9.5 7.9.5 7.9.5s6 0 7.9-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 22.5 12a31 31 0 0 0-.5-3.8ZM10 15.2V8.8l5.5 3.2z" fill="currentColor" stroke="none"/>@break
                                @case('pinterest')<path d="M12 2a10 10 0 0 0-3.6 19.3c0-.8 0-1.8.2-2.7l1.3-5.4s-.3-.6-.3-1.5c0-1.4.8-2.5 1.8-2.5.9 0 1.3.7 1.3 1.5 0 .9-.6 2.2-.9 3.5-.3 1 .5 1.8 1.5 1.8 1.8 0 3.2-1.9 3.2-4.6 0-2.4-1.7-4.1-4.2-4.1-2.9 0-4.6 2.2-4.6 4.4 0 .9.3 1.9.8 2.4.1.1.1.2.1.4l-.3 1.1c0 .2-.2.3-.4.2-1.3-.6-2.1-2.3-2.1-3.7 0-3 2.2-5.8 6.5-5.8 3.4 0 6 2.4 6 5.6 0 3.3-2.1 6.1-5.1 6.1-1 0-2-.6-2.3-1.2l-.7 2.7c-.2 1-.9 2-1.3 2.7A10 10 0 1 0 12 2Z" fill="currentColor" stroke="none"/>@break
                            @endswitch
                        </svg>
                    </a>
                @endif
            @endforeach
        </div>
        <span>{{ $settings?->site_name ?? 'Architecture Portfolio' }}</span>
        <span>© {{ now()->year }}</span>
        <a class="admin-login-link" href="{{ route('admin.login') }}">Administrator sign in</a>
    </footer>

    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
