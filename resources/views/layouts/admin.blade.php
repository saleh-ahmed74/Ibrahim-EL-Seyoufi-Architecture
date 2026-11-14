<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} · Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="admin-body">
    <header class="admin-header">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}">Portfolio Admin</a>
        <nav aria-label="Administration">
            <a href="{{ route('admin.dashboard') }}">Overview</a>
            <a href="{{ route('admin.projects') }}">Projects</a>
            <a href="{{ route('admin.categories') }}">Categories</a>
            <a href="{{ route('admin.settings') }}">Site settings</a>
            <a href="{{ route('admin.inquiries') }}">Inquiries</a>
            <a href="{{ route('admin.analytics') }}">Analytics</a>
        </nav>
        <div class="admin-account">
            <span>{{ auth()->user()->name }}</span>
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">View site</a>
            <form method="post" action="{{ route('admin.logout') }}">@csrf<button type="submit">Sign out</button></form>
        </div>
    </header>
    <main class="admin-main">
        @if (session('status'))
            <div class="flash-message" role="status">{{ session('status') }}</div>
        @endif
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
