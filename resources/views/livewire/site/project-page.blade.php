@php
    $hasSubprojects = $project->children->isNotEmpty();
    $heroCount = $hasSubprojects ? $project->children->count() : $project->images->count();
    $displayCategory = $project->parent?->category?->name ?: $project->category?->name ?: 'Architecture';
@endphp
<article class="project-detail">
    <section class="project-feature-hero" aria-label="{{ $project->title }} project gallery" x-data="{ active: 0, count: {{ $heroCount }}, paused: false, timer: null,
        start() { if (this.count > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.timer = window.setInterval(() => { if (!this.paused && !document.hidden) this.active = (this.active + 1) % this.count }, 1000) },
        destroy() { if (this.timer) window.clearInterval(this.timer) },
        next() { this.active = (this.active + 1) % this.count },
        previous() { this.active = (this.active - 1 + this.count) % this.count } }"
        x-init="start()" x-on:mouseenter="paused = true" x-on:mouseleave="paused = false" x-on:focusin="paused = true" x-on:focusout="paused = false">
        <div class="project-feature-slides" aria-hidden="true">
            @if($hasSubprojects)
                @foreach($project->children as $index => $child)
                    @php $childCover = $child->coverImage ?: $child->images->first(); @endphp
                    @if($childCover)
                        <img x-show="active === {{ $index }}" x-cloak src="{{ $childCover->publicUrl() }}" alt="" @if($index > 0) loading="lazy" @endif>
                    @else
                        <div class="image-placeholder" x-show="active === {{ $index }}" x-cloak></div>
                    @endif
                @endforeach
            @else
                @foreach($project->images as $index => $image)
                    <img x-show="active === {{ $index }}" x-cloak src="{{ $image->publicUrl() }}" alt="" @if($index > 0) loading="lazy" @endif>
                @endforeach
            @endif
        </div>
        <div class="project-feature-shade"></div>
        <div class="project-feature-copy">
            @if($project->parent)<a class="project-back-link" href="{{ route('projects.show', $project->parent->slug) }}">← {{ $project->parent->title }}</a>@endif
            <span class="project-feature-kicker">{{ $displayCategory }} @if($project->year)<i></i>{{ $project->year }}@endif</span>
            <h1>{{ $project->title }}</h1>
            @if($project->location)<p class="project-feature-location">{{ $project->location }}</p>@endif
            @if($project->description)<p class="project-feature-intro">{{ \Illuminate\Support\Str::limit(strip_tags($project->description), 190) }}</p>@endif
            @if($hasSubprojects)<a class="project-feature-cta" href="#subprojects">Explore {{ $project->children->count() }} {{ $project->children->count() === 1 ? 'subproject' : 'subprojects' }} <span>↓</span></a>@endif
        </div>
        @if($heroCount > 1)
            <div class="project-feature-controls" aria-label="Gallery controls">
                <button type="button" x-on:click="previous()" aria-label="Previous image">←</button>
                <span><b x-text="String(active + 1).padStart(2, '0')"></b><i></i>{{ str_pad((string) $heroCount, 2, '0', STR_PAD_LEFT) }}</span>
                <button type="button" x-on:click="next()" aria-label="Next image">→</button>
                <button class="project-gallery-pause" type="button" x-on:click="paused = !paused" x-text="paused ? 'Play' : 'Pause'">Pause</button>
            </div>
        @endif
    </section>

    @if($hasSubprojects)
        <section class="subprojects-section" id="subprojects">
            <header class="subprojects-heading"><div><span class="eyebrow">{{ $project->children->count() }} {{ $project->children->count() === 1 ? 'part of the project' : 'parts of the project' }}</span><h2>Inside {{ $project->title }}</h2><p>Explore the spaces, buildings, and design stages that make up this project.</p></div></header>
            <div class="subproject-card-grid">
                @foreach($project->children as $index => $child)
                    @php $childCover = $child->coverImage ?: $child->images->first(); @endphp
                    <a class="subproject-card" href="{{ route('projects.show', $child->slug) }}">
                        <div class="subproject-card-image">@if($childCover)<img src="{{ $childCover->publicUrl() }}" alt="{{ $childCover->alt_text ?: $child->title }}" loading="lazy">@else<div class="image-placeholder"></div>@endif<span class="subproject-card-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><span class="subproject-card-open" aria-hidden="true">↗</span></div>
                        <div class="subproject-card-copy"><span class="subproject-card-meta">{{ $child->location ?: $displayCategory }} @if($child->year)<i></i>{{ $child->year }}@endif</span><h3>{{ $child->title }}</h3>@if($child->description)<p>{{ \Illuminate\Support\Str::limit(strip_tags($child->description), 150) }}</p>@endif<span class="subproject-card-link">View project <b>↗</b></span></div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($project->description)
        <section class="project-description-section"><span class="eyebrow">Project notes</span><div class="description prose">{{ $project->description }}</div></section>
    @endif

    @if($project->images->isNotEmpty())
        <section class="project-photo-section"><header class="subprojects-heading"><div><span class="eyebrow">Selected views</span><h2>{{ $hasSubprojects ? 'Main project gallery' : 'Project gallery' }}</h2></div></header><div class="project-photo-grid">
            @foreach($project->images as $index => $image)
                <figure><img src="{{ $image->publicUrl() }}" alt="{{ $image->alt_text ?: $project->title.' '.($index + 1) }}" loading="lazy">@if($image->alt_text)<figcaption>{{ $image->alt_text }}</figcaption>@endif</figure>
            @endforeach
        </div></section>
    @endif
</article>