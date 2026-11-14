@php
    $faviconSettings = app(\App\Support\PublicContent::class)->settings();
    $faviconUrl = $faviconSettings->publicImageUrl($faviconSettings->logo_path);
@endphp
<link rel="icon" href="{{ $faviconUrl ?: asset('favicon.svg') }}" sizes="any">
<link rel="apple-touch-icon" href="{{ $faviconUrl ?: asset('favicon.svg') }}">
