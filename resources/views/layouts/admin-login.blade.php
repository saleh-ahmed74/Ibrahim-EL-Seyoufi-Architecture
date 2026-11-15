<!doctype html>
<html lang="en" dir="ltr" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('layouts.partials.favicon')
    <title>Administrator sign in · Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="admin-login-body">
    <button class="admin-login-theme-toggle" type="button" data-theme-toggle data-theme-storage="admin_theme_v2" data-theme-default="light" data-light-label="Dark mode" data-dark-label="Light mode" aria-label="Change administrator color mode">Dark mode</button>
    <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
    <main class="admin-login-wrap">{{ $slot }}</main>
    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
