@php
    $hasSubprojects = $project->children_count > 0;
    $galleryItems = $hasSubprojects
        ? $children->getCollection()->map(fn ($child) => ($child->coverImage ?: $child->images->first())?->publicUrl())->values()
        : $images->getCollection()->map(fn ($image) => $image->publicUrl())->values();
    $heroCount = $galleryItems->count();
    $displayCategory = $project->parent?->category?->name ?: $project->category?->name ?: 'Architecture';
@endphp
<article class="project-detail" x-data="{ lightboxOpen: false, lightboxIndex: 0, lightboxImages: @js($images->getCollection()->values()->map(fn ($image, $index) => ['url' => $image->publicUrl(), 'alt' => $image->alt_text ?: $project->title.' '.($index + 1)])), openLightbox(index) { this.lightboxIndex = index; this.lightboxOpen = true; document.body.classList.add('lightbox-open') }, closeLightbox() { this.lightboxOpen = false; document.body.classList.remove('lightbox-open') }, moveLightbox(step) { this.lightboxIndex = (this.lightboxIndex + step + this.lightboxImages.length) % this.lightboxImages.length } }" x-on:keydown.escape.window="if (lightboxOpen) closeLightbox()" x-on:keydown.arrow-right.window="if (lightboxOpen) moveLightbox(1)" x-on:keydown.arrow-left.window="if (lightboxOpen) moveLightbox(-1)" x-on:lightbox-open.window="openLightbox($event.detail.index)">
    <section class="project-feature-hero" aria-label="{{ $project->title }} project gallery" x-data="{ active: 0, count: {{ $heroCount }}, galleryItems: @js($galleryItems), paused: false, timer: null, dragStartX: null,
        start() { if (this.count > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.timer = window.setInterval(() => { if (!this.paused && !document.hidden) this.active = (this.active + 1) % this.count }, 5000) },
        destroy() { if (this.timer) window.clearInterval(this.timer) },
        next() { this.active = (this.active + 1) % this.count },
        previous() { this.active = (this.active - 1 + this.count) % this.count },
        startDrag(event) { if (this.count < 2 || event.target.closest('a,button')) return; this.dragStartX = event.clientX; event.currentTarget.setPointerCapture(event.pointerId) },
        endDrag(event) { if (this.dragStartX === null) return; const distance = event.clientX - this.dragStartX; if (Math.abs(distance) > 45) distance < 0 ? this.next() : this.previous(); this.dragStartX = null } }"
        x-init="start()" x-on:mouseenter="paused = true" x-on:mouseleave="paused = false" x-on:focusin="paused = true" x-on:focusout="paused = false" x-on:pointerdown="startDrag($event)" x-on:pointerup="endDrag($event)" x-on:pointercancel="dragStartX = null">
        <div class="project-feature-slides" aria-hidden="true">
            @if($hasSubprojects)
                @foreach($children as $index => $child)
                    @php $childCover = $child->coverImage ?: $child->images->first(); @endphp
                    @if($childCover)
                        <img x-show="active === {{ $index }}" x-cloak @if($index === 0) src="{{ $childCover->publicUrl() }}" fetchpriority="high" @endif x-bind:src="active === {{ $index }} ? @js($childCover->publicUrl()) : null" alt="" loading="lazy" decoding="async">
                    @else
                        <div class="image-placeholder" x-show="active === {{ $index }}" x-cloak></div>
                    @endif
                @endforeach
            @else
                @foreach($images as $index => $image)
                    <img x-show="active === {{ $index }}" x-cloak @if($index === 0) src="{{ $image->publicUrl() }}" fetchpriority="high" @endif x-bind:src="active === {{ $index }} ? @js($image->publicUrl()) : null" alt="" loading="lazy" decoding="async">
                @endforeach
            @endif
            @if($heroCount > 1)
                <img class="project-feature-preview project-feature-preview-previous" x-show="galleryItems[(active - 1 + count) % count]" x-bind:src="galleryItems[(active - 1 + count) % count] || null" alt="" aria-hidden="true" draggable="false">
                <img class="project-feature-preview project-feature-preview-next" x-show="galleryItems[(active + 1) % count]" x-bind:src="galleryItems[(active + 1) % count] || null" alt="" aria-hidden="true" draggable="false">
            @endif
        </div>
        <div class="project-feature-shade"></div>
        <div class="project-feature-copy">
            @if($project->parent)<a class="project-back-link" href="{{ route('projects.show', $project->parent->slug) }}">← {{ $project->parent->title }}</a>@endif
            <span class="project-feature-kicker">{{ $displayCategory }} @if($project->year)<i></i>{{ $project->year }}@endif</span>
            <h1>{{ $project->title }}</h1>
            @if($project->location)<p class="project-feature-location">{{ $project->location }}</p>@endif
            @if($project->description)<p class="project-feature-intro">{{ \Illuminate\Support\Str::limit(strip_tags($project->description), 190) }}</p>@endif
            @if($hasSubprojects)<a class="project-feature-cta" href="#subprojects">Explore {{ $project->children_count }} {{ $project->children_count === 1 ? 'subproject' : 'subprojects' }} <span>↓</span></a>@endif
        </div>
    </section>

    @if($hasSubprojects)
        <section class="subprojects-section" id="subprojects">
            <header class="subprojects-heading"><div><span class="eyebrow">{{ $project->children_count }} {{ $project->children_count === 1 ? 'part of the project' : 'parts of the project' }}</span><h2>Inside {{ $project->title }}</h2><p>Explore the spaces, buildings, and design stages that make up this project.</p></div></header>
            <div class="subproject-card-grid">
                @foreach($children as $index => $child)
                    @php $childCover = $child->coverImage ?: $child->images->first(); @endphp
                    <a class="subproject-card" href="{{ route('projects.show', $child->slug) }}">
                        <div class="subproject-card-image">@if($childCover)<img src="{{ $childCover->publicUrl() }}" alt="{{ $childCover->alt_text ?: $child->title }}" loading="lazy">@else<div class="image-placeholder"></div>@endif<span class="subproject-card-number">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</span><span class="subproject-card-open" aria-hidden="true">↗</span></div>
                        <div class="subproject-card-copy"><span class="subproject-card-meta">{{ $child->location ?: $displayCategory }} @if($child->year)<i></i>{{ $child->year }}@endif</span><h3>{{ $child->title }}</h3>@if($child->description)<p>{{ \Illuminate\Support\Str::limit(strip_tags($child->description), 150) }}</p>@endif<span class="subproject-card-link">View project <b>↗</b></span></div>
                    </a>
                @endforeach
            </div>
            @if($children->hasPages())
                <nav class="portfolio-pagination" aria-label="Subproject pages">{{ $children->links(data: ['scrollTo' => '#subprojects']) }}</nav>
            @endif
        </section>
    @endif

    @if($project->description)
        <section class="project-description-section"><span class="eyebrow">Project notes</span><div class="description prose">{{ $project->description }}</div></section>
    @endif

    @if($project->images_count > 0)
        <section class="project-photo-section"><header class="subprojects-heading"><div><span class="eyebrow">Selected views</span><h2>{{ $hasSubprojects ? 'Main project gallery' : 'Project gallery' }}</h2></div></header><div class="project-photo-grid">
            @foreach($images as $index => $image)
                <figure><button class="project-photo-zoom" type="button" x-on:click="$dispatch('lightbox-open', { index: {{ $index }} })" aria-label="Zoom image {{ $index + 1 }}: {{ $image->alt_text ?: $project->title }}"><img src="{{ $image->publicUrl() }}" alt="{{ $image->alt_text ?: $project->title.' '.($index + 1) }}" loading="lazy" decoding="async"><span aria-hidden="true">＋</span></button>@if($image->alt_text)<figcaption>{{ $image->alt_text }}</figcaption>@endif</figure>
            @endforeach
        </div></section>
        @if($images->hasPages())
            <nav class="portfolio-pagination" aria-label="Gallery pages">{{ $images->links(data: ['scrollTo' => false]) }}</nav>
        @endif
    @endif
    <div class="photo-lightbox" x-show="lightboxOpen" x-cloak x-transition.opacity x-on:click.self="closeLightbox()" role="dialog" aria-modal="true" aria-label="Project image viewer">
        <button class="photo-lightbox-close" type="button" x-on:click="closeLightbox()" aria-label="Close image viewer">×</button>
        <button class="photo-lightbox-arrow photo-lightbox-previous" type="button" x-on:click="moveLightbox(-1)" aria-label="Previous image">←</button>
        <figure><img x-bind:src="lightboxImages[lightboxIndex]?.url" x-bind:alt="lightboxImages[lightboxIndex]?.alt" decoding="async"><figcaption x-text="lightboxImages[lightboxIndex]?.alt"></figcaption></figure>
        <button class="photo-lightbox-arrow photo-lightbox-next" type="button" x-on:click="moveLightbox(1)" aria-label="Next image">→</button>
        <span class="photo-lightbox-count" x-text="`${lightboxIndex + 1} / ${lightboxImages.length}`"></span>
    </div>
</article>
