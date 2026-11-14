<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administrator sign in · Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="admin-login-body">
    <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
    <main class="admin-login-wrap">{{ $slot }}</main>
    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
