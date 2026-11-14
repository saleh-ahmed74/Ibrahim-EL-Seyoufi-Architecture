<!doctype html>
<html lang="en" dir="ltr" data-theme="light">
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

    <header class="nav site-nav">
        <a class="site-brand" href="{{ route('home') }}" aria-label="{{ $settings?->site_name ?? 'Home' }}">
            @if ($logoUrl)
                <img class="site-logo" src="{{ $logoUrl }}" alt="{{ $settings?->site_name ?? 'Site logo' }}">
            @else
                <span class="wordmark">{{ $settings?->site_name ?? 'Architecture' }}</span>
            @endif
        </a>
        <nav class="site-menu" aria-label="Main navigation">
            <a href="{{ route('home') }}" data-en="Home" data-ar="الرئيسية">Home</a>
            <a href="{{ route('projects.index') }}" data-en="Projects" data-ar="المشاريع">Projects</a>
            <a href="{{ route('about') }}" data-en="About" data-ar="من نحن">About</a>
        </nav>
        <div class="site-actions">
            <button type="button" class="text-action" data-language-toggle aria-label="Change language">العربية</button>
            <button type="button" class="text-action" data-theme-toggle aria-label="Toggle color theme">◐</button>
        </div>
    </header>

    <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
    <main>{{ $slot }}</main>

    @if ($whatsapp)
        <a class="whatsapp-fab" href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer" aria-label="Contact on WhatsApp">↗</a>
    @endif

    <footer class="site-footer">
        <div class="footer-socials">
            @foreach (['instagram' => $settings?->instagram_url, 'facebook' => $settings?->facebook_url, 'linkedin' => $settings?->linkedin_url, 'youtube' => $settings?->youtube_url, 'pinterest' => $settings?->pinterest_url] as $network => $url)
                @if ($url)
                    <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ ucfirst($network) }}</a>
                @endif
            @endforeach
        </div>
        <span>{{ $settings?->site_name ?? 'Architecture Portfolio' }}</span>
        <span>© {{ now()->year }}</span>
    </footer>

    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
