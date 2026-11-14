<div class="home-page">
<section class="hero" aria-label="Featured architecture work">
    @if ($heroSlides->isNotEmpty())
        <div class="hero-slideshow" x-data="{ active: 0, count: {{ $heroSlides->count() }} }" @if($heroSlides->count() > 1) x-init="setInterval(() => active = (active + 1) % count, 3200)" @endif>
            @foreach ($heroSlides as $index => $slide)
                <img x-bind:class="active === {{ $index }} ? 'slide active' : 'slide'" x-show="active === {{ $index }}" x-cloak src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}" @if($index > 0) loading="lazy" @endif>
            @endforeach
        </div>
    @else
        <div class="hero-placeholder" aria-hidden="true"></div>
    @endif
    <div class="hero-copy">
        <span class="eyebrow">Independent practice · Cairo</span>
        <h1>{{ $settings->architect_name ?: $settings->site_name }}</h1>
        <p class="title">{{ $settings->professional_title }}</p>
        @if ($settings->short_bio)<p class="intro">{{ $settings->short_bio }}</p>@endif
        <div class="cta-row">
            <a class="button" href="{{ route('projects.index') }}">View projects</a>
            @if ($settings->whatsapp_number)
                <a class="button secondary" href="https://wa.me/{{ preg_replace('/\D/', '', $settings->whatsapp_number) }}?text={{ urlencode($settings->whatsapp_message ?? '') }}" target="_blank" rel="noopener noreferrer">Get in touch ↗</a>
            @endif
        </div>
    </div>
    <a class="hero-scroll" href="#selected-work" aria-label="Scroll to selected projects">↓</a>
</section>

<section class="section" id="selected-work">
    <div class="section-head">
        <div><span class="eyebrow">Selected work</span><h2>Projects</h2></div>
        <a class="text-link" href="{{ route('projects.index') }}">All projects →</a>
    </div>

    @if ($categories->isNotEmpty())
        <div class="filter-bar">
            <label class="eyebrow" for="home-category">Filter</label>
            <select id="home-category" wire:model.live="category" aria-label="Filter projects by category">
                <option value="">All work</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption->slug }}">{{ $categoryOption->name }}</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="projects-grid">
        @forelse ($projects as $project)
            @include('livewire.site.project-card', ['project' => $project])
        @empty
            <p class="empty-state">No projects are available in this category yet.</p>
        @endforelse
    </div>
</section>

<section class="about-band">
    <span class="eyebrow">Practice</span>
    <p>{{ $settings->short_bio ?: $settings->bio }}</p>
    <a class="text-link" href="{{ route('about') }}">More about the studio →</a>
</section>
</div>
