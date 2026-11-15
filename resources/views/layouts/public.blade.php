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
        $homepageCopy = \App\Support\HomepageCopy::merge($settings?->homepage_copy);
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
        <button class="site-menu-toggle" type="button" data-en-open-label="{{ $homepageCopy['open_navigation_label']['en'] }}" data-ar-open-label="{{ $homepageCopy['open_navigation_label']['ar'] }}" data-en-close-label="{{ $homepageCopy['close_navigation_label']['en'] }}" data-ar-close-label="{{ $homepageCopy['close_navigation_label']['ar'] }}" x-bind:aria-label="menuOpen ? $el.dataset.closeLabel : $el.dataset.openLabel" x-on:click="menuOpen = !menuOpen" x-bind:aria-expanded="menuOpen.toString()">☰</button>
        @if ($settings?->instagram_url || $settings?->facebook_url || $settings?->linkedin_url || $settings?->youtube_url || $settings?->pinterest_url)
            <div class="header-socials" aria-label="{{ $homepageCopy['social_media_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['social_media_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['social_media_label']['ar'] }}">
                @include('layouts.partials.social-links')
            </div>
        @endif
        <nav class="site-menu" aria-label="{{ $homepageCopy['main_navigation_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['main_navigation_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['main_navigation_label']['ar'] }}" x-bind:class="menuOpen ? 'is-open' : ''">
            <span class="site-menu-section-label" data-en="{{ $homepageCopy['pages_label']['en'] }}" data-ar="{{ $homepageCopy['pages_label']['ar'] }}">{{ $homepageCopy['pages_label']['en'] }}</span>
            <a @class(['is-current' => request()->routeIs('home')]) href="{{ route('home') }}" data-en="{{ $homepageCopy['home_link']['en'] }}" data-ar="{{ $homepageCopy['home_link']['ar'] }}">{{ $homepageCopy['home_link']['en'] }}</a>
            <a @class(['is-current' => request()->routeIs('projects.*')]) href="{{ route('projects.index') }}" data-en="{{ $homepageCopy['projects_link']['en'] }}" data-ar="{{ $homepageCopy['projects_link']['ar'] }}">{{ $homepageCopy['projects_link']['en'] }}</a>
            @livewire('site.category-menu', ['menuCopy' => [
                'categories_link' => $homepageCopy['categories_link'],
                'all_projects' => $homepageCopy['all_projects'],
                'loading_categories' => $homepageCopy['loading_categories'],
                'no_categories' => $homepageCopy['no_categories'],
            ]])
            <a @class(['is-current' => request()->routeIs('about')]) href="{{ route('about') }}" data-en="{{ $homepageCopy['about_nav_link']['en'] }}" data-ar="{{ $homepageCopy['about_nav_link']['ar'] }}">{{ $homepageCopy['about_nav_link']['en'] }}</a>
            @guest
                <a @class(['is-current' => request()->routeIs('login')]) href="{{ route('login') }}" data-en="{{ $homepageCopy['sign_in_link']['en'] }}" data-ar="{{ $homepageCopy['sign_in_link']['ar'] }}">{{ $homepageCopy['sign_in_link']['en'] }}</a>
                <a @class(['is-current' => request()->routeIs('register')]) href="{{ route('register') }}" data-en="{{ $homepageCopy['sign_up_link']['en'] }}" data-ar="{{ $homepageCopy['sign_up_link']['ar'] }}">{{ $homepageCopy['sign_up_link']['en'] }}</a>
            @else
                <a @class(['is-current' => request()->routeIs('account.password')]) href="{{ route('account.password') }}" data-en="{{ $homepageCopy['account_link']['en'] }}" data-ar="{{ $homepageCopy['account_link']['ar'] }}">{{ $homepageCopy['account_link']['en'] }}</a>
                <form method="post" action="{{ route('logout') }}" class="site-logout-form">@csrf<button type="submit" data-en="{{ $homepageCopy['sign_out_link']['en'] }}" data-ar="{{ $homepageCopy['sign_out_link']['ar'] }}">{{ $homepageCopy['sign_out_link']['en'] }}</button></form>
            @endguest
            <span class="site-menu-section-label site-menu-preferences-label" data-en="{{ $homepageCopy['preferences_label']['en'] }}" data-ar="{{ $homepageCopy['preferences_label']['ar'] }}">{{ $homepageCopy['preferences_label']['en'] }}</span>
            <div class="site-menu-controls" aria-label="{{ $homepageCopy['site_preferences_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['site_preferences_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['site_preferences_label']['ar'] }}">
                <button type="button" class="site-menu-option" data-language-toggle data-en-label="{{ $homepageCopy['language_toggle_en']['en'] }}" data-ar-label="{{ $homepageCopy['language_toggle_ar']['ar'] }}" aria-label="{{ $homepageCopy['change_language_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['change_language_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['change_language_label']['ar'] }}">{{ $homepageCopy['language_toggle_en']['en'] }}</button>
                <button type="button" class="site-menu-option" data-theme-toggle data-light-label-en="{{ $homepageCopy['dark_mode_label']['en'] }}" data-light-label-ar="{{ $homepageCopy['dark_mode_label']['ar'] }}" data-dark-label-en="{{ $homepageCopy['light_mode_label']['en'] }}" data-dark-label-ar="{{ $homepageCopy['light_mode_label']['ar'] }}" aria-label="{{ $homepageCopy['change_theme_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['change_theme_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['change_theme_label']['ar'] }}">{{ $homepageCopy['light_mode_label']['en'] }}</button>
            </div>
        </nav>
    </header>

    <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
    <main>{{ $slot }}</main>

    @if ($whatsapp)
        <a class="whatsapp-fab" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $homepageCopy['whatsapp_accessible_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['whatsapp_accessible_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['whatsapp_accessible_label']['ar'] }}" title="WhatsApp">
            <svg viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                <path d="M16 3.2a12.65 12.65 0 0 0-10.8 19.2L3.5 28.5l6.3-1.65A12.65 12.65 0 1 0 16 3.2Z" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M12 9.8c-.35-.78-.73-.8-1.08-.8h-.92c-.32 0-.84.12-1.28.6-.44.47-1.68 1.64-1.68 4s1.72 4.63 1.96 4.95c.24.31 3.33 5.32 8.22 7.24 4.05 1.6 4.88 1.28 5.76 1.2.88-.08 2.84-1.16 3.24-2.28.4-1.12.4-2.08.28-2.28-.12-.2-.44-.32-.92-.56s-2.84-1.4-3.28-1.56c-.44-.16-.76-.24-1.08.24-.32.48-1.24 1.56-1.52 1.88-.28.32-.56.36-1.04.12-.48-.24-2.02-.75-3.85-2.38-1.42-1.27-2.38-2.84-2.66-3.32-.28-.48-.03-.74.21-.98.21-.21.48-.56.72-.84.24-.28.32-.48.48-.8.16-.32.08-.6-.04-.84-.12-.24-1.05-2.6-1.45-3.54Z" fill="currentColor" transform="translate(4 0) scale(.76)"/>
            </svg>
        </a>
    @endif

    <footer class="site-footer">
        <div class="footer-socials" aria-label="Social media">
            <span class="socials-label" data-en="{{ $homepageCopy['follow_studio']['en'] }}" data-ar="{{ $homepageCopy['follow_studio']['ar'] }}">{{ $homepageCopy['follow_studio']['en'] }}</span>
            @include('layouts.partials.social-links')
        </div>
        <div class="footer-brand-about">
            @if($logoUrl)<img class="footer-logo" src="{{ $logoUrl }}" alt="{{ $settings?->site_name ?? 'Site logo' }}" loading="lazy">@else<span class="footer-site-name">{{ $settings?->site_name ?? 'Architecture Portfolio' }}</span>@endif
            <a class="footer-about-link" href="{{ route('about') }}"><span data-en="{{ $homepageCopy['about_link']['en'] }}" data-ar="{{ $homepageCopy['about_link']['ar'] }}">{{ $homepageCopy['about_link']['en'] }}</span><span aria-hidden="true">→</span></a>
        </div>
        <span>© {{ now()->year }}</span>
        <a class="admin-login-link" href="{{ route('admin.login') }}" data-en="{{ $homepageCopy['admin_sign_in']['en'] }}" data-ar="{{ $homepageCopy['admin_sign_in']['ar'] }}">{{ $homepageCopy['admin_sign_in']['en'] }}</a>
    </footer>

    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
