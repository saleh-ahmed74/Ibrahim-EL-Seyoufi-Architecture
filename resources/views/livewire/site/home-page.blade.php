<div class="home-page">
<section class="hero" aria-label="{{ $homepageCopy['hero_accessible_label']['en'] }}" data-en-aria-label="{{ $homepageCopy['hero_accessible_label']['en'] }}" data-ar-aria-label="{{ $homepageCopy['hero_accessible_label']['ar'] }}">
    @if ($heroSlides->isNotEmpty())
        <div class="hero-slideshow" x-data="{ active: 0, count: {{ $heroSlides->count() }} }" @if($heroSlides->count() > 1) x-init="setInterval(() => { if (!document.hidden && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) active = (active + 1) % count }, 5200)" @endif>
            @foreach ($heroSlides as $index => $slide)
                <img x-bind:class="active === {{ $index }} ? 'slide active' : 'slide'" x-show="active === {{ $index }}" x-cloak x-bind:src="active === {{ $index }} ? @js($slide['url']) : null" alt="{{ $slide['alt'] }}" loading="lazy" decoding="async" @if($index === 0) fetchpriority="high" @endif>
            @endforeach
        </div>
    @else
        <div class="hero-placeholder" aria-hidden="true"></div>
    @endif
    @if($heroPortrait)
        <img class="home-hero-person" src="{{ $heroPortrait }}" alt="{{ $settings->site_name }}" loading="eager" fetchpriority="high" decoding="async">
    @endif
    @if($settings->logo_path)
        <a class="home-hero-brand" href="{{ route('home') }}" aria-label="{{ $settings->site_name }}">
            <img class="home-hero-logo" src="{{ $settings->publicImageUrl($settings->logo_path) }}" alt="{{ $settings->site_name }}" loading="eager" decoding="async">
        </a>
    @endif
    <div class="hero-copy">
        <span class="eyebrow" data-en="{{ $homepageCopy['hero_eyebrow']['en'] }}" data-ar="{{ $homepageCopy['hero_eyebrow']['ar'] }}">{{ $homepageCopy['hero_eyebrow']['en'] }}</span>
        @php($heroNameParts = preg_split('/\s+/', trim($settings->architect_name ?: $settings->site_name), 2) ?: [])
        <h1><span>{{ $heroNameParts[0] ?? '' }}</span>@if (! empty($heroNameParts[1])) <span class="hero-name-second">{{ $heroNameParts[1] }}</span>@endif</h1>
        <p class="title">{{ $settings->professional_title }}</p>
        @if ($settings->short_bio)<p class="intro">{{ $settings->short_bio }}</p>@endif
        <div class="cta-row">
            <a class="button hero-projects-button" href="{{ route('projects.index') }}"><span data-en="{{ $homepageCopy['view_projects']['en'] }}" data-ar="{{ $homepageCopy['view_projects']['ar'] }}">{{ $homepageCopy['view_projects']['en'] }}</span><span class="button-arrow" aria-hidden="true">↗</span></a>
            @if ($settings->whatsapp_number)
                <a class="button secondary" href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}?text={{ urlencode($settings->whatsapp_message ?? '') }}" target="_blank" rel="noopener noreferrer"><span data-en="{{ $homepageCopy['get_in_touch']['en'] }}" data-ar="{{ $homepageCopy['get_in_touch']['ar'] }}">{{ $homepageCopy['get_in_touch']['en'] }}</span> ↗</a>
            @endif
        </div>
    </div>
    <a class="hero-scroll" href="#selected-work" aria-label="{{ $homepageCopy['scroll_to_projects']['en'] }}" data-en-aria-label="{{ $homepageCopy['scroll_to_projects']['en'] }}" data-ar-aria-label="{{ $homepageCopy['scroll_to_projects']['ar'] }}">↓</a>
</section>

<section class="section" id="selected-work">
    <div class="section-head">
        <div><span class="eyebrow" data-en="{{ $homepageCopy['selected_eyebrow']['en'] }}" data-ar="{{ $homepageCopy['selected_eyebrow']['ar'] }}">{{ $homepageCopy['selected_eyebrow']['en'] }}</span><h2 data-en="{{ $homepageCopy['projects_heading']['en'] }}" data-ar="{{ $homepageCopy['projects_heading']['ar'] }}">{{ $homepageCopy['projects_heading']['en'] }}</h2></div>
        <a class="button secondary all-projects-button" href="{{ route('projects.index') }}"><span data-en="{{ $homepageCopy['all_projects']['en'] }}" data-ar="{{ $homepageCopy['all_projects']['ar'] }}">{{ $homepageCopy['all_projects']['en'] }}</span><span aria-hidden="true">↗</span></a>
    </div>

    <div class="projects-grid home-projects-grid">
        @forelse ($projects as $project)
            @include('livewire.site.project-card', ['project' => $project, 'homepageCopy' => $homepageCopy])
        @empty
            <p class="empty-state" data-en="{{ $homepageCopy['empty_projects']['en'] }}" data-ar="{{ $homepageCopy['empty_projects']['ar'] }}">{{ $homepageCopy['empty_projects']['en'] }}</p>
        @endforelse
    </div>
</section>

</div>
