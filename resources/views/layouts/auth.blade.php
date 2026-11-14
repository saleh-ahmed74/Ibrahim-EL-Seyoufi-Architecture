<!doctype html>
<html lang="en" dir="ltr">
@php
    $authSettings = app(\App\Support\PublicContent::class)->settings();
    $authLogo = $authSettings->publicImageUrl($authSettings->logo_path);
@endphp
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Account' }} · {{ $authSettings->site_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="auth-body">
    <header class="auth-header">
        <a class="site-brand" href="{{ route('home') }}" aria-label="{{ $authSettings->site_name }}">
            @if ($authLogo)
                <img class="site-logo" src="{{ $authLogo }}" alt="{{ $authSettings->site_name }}">
            @else
                <span class="wordmark">{{ $authSettings->site_name }}</span>
            @endif
        </a>
        <nav aria-label="Account navigation">
            <a href="{{ route('home') }}">Website</a>
            @guest
                <a href="{{ route('login') }}">Sign in</a>
                <a href="{{ route('register') }}">Sign up</a>
            @else
                <a href="{{ route('account.password') }}">Change password</a>
                <form method="post" action="{{ route('logout') }}">@csrf<button type="submit">Sign out</button></form>
            @endguest
        </nav>
    </header>
    <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
    <main class="auth-main">
        @if (session('status'))
            <div class="flash-message" role="status">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
