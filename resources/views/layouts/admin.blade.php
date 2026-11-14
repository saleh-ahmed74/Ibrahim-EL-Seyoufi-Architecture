<!doctype html>
<html lang="en" dir="ltr" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} · Portfolio</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="admin-body">
    <div class="admin-shell" x-data="{ menuOpen: false }" x-on:keydown.escape.window="menuOpen = false" x-on:focus-project-editor.window="$nextTick(() => $refs.projectEditor?.scrollIntoView({behavior: 'smooth', block: 'start'}))">
        <aside class="admin-sidebar" x-bind:class="menuOpen ? 'is-open' : ''">
            <a class="admin-brand" href="{{ route('admin.dashboard') }}">Portfolio Admin</a>
            <span class="admin-sidebar-label">Manage portfolio</span>
            <nav class="admin-sidebar-nav" aria-label="Administration" x-on:click="menuOpen = false">
                <a @class(['active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}"><span>▦</span> Overview</a>
                <a @class(['active' => request()->routeIs('admin.projects')]) href="{{ route('admin.projects') }}"><span>▧</span> Projects & galleries</a>
                <a @class(['active' => request()->routeIs('admin.categories')]) href="{{ route('admin.categories') }}"><span>◈</span> Categories</a>
                <a @class(['active' => request()->routeIs('admin.settings')]) href="{{ route('admin.settings') }}"><span>⚙</span> Site settings</a>
                <a @class(['active' => request()->routeIs('admin.inquiries')]) href="{{ route('admin.inquiries') }}"><span>✉</span> Inquiries</a>
                <a @class(['active' => request()->routeIs('admin.analytics')]) href="{{ route('admin.analytics') }}"><span>▥</span> Analytics</a>
            </nav>
            <div class="admin-sidebar-account">
                <span class="admin-sidebar-user">{{ auth()->user()->name }}</span>
                <a href="{{ route('account.password') }}">Change password</a>
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">View public website ↗</a>
                <form method="post" action="{{ route('admin.logout') }}">@csrf<button class="admin-sign-out" type="submit">Sign out</button></form>
            </div>
        </aside>
        <button class="admin-sidebar-scrim" type="button" aria-label="Close administration menu" x-show="menuOpen" x-on:click="menuOpen = false" x-cloak></button>
        <div class="admin-content">
            <header class="admin-topbar">
                <button class="admin-menu-toggle" type="button" aria-label="Open administration menu" x-on:click="menuOpen = !menuOpen" x-bind:aria-expanded="menuOpen.toString()">☰</button>
                <span>{{ $title ?? 'Portfolio administration' }}</span>
            </header>
            <div class="network-status" id="network-status" role="status" aria-live="polite" hidden></div>
            <main class="admin-main">
                @if (session('status'))
                    <div class="flash-message" role="status">{{ session('status') }}</div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
    @livewireScripts
    <script src="{{ asset('offline.js') }}" defer></script>
</body>
</html>
