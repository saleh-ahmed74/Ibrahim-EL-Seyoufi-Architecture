<article class="project-detail">
    <header class="project-detail-heading">
        @if ($project->parent)
            <a class="back-link" href="{{ route('projects.show', $project->parent->slug) }}">← {{ $project->parent->title }}</a>
        @endif
        <span class="eyebrow">{{ $project->category?->name ?: 'Architecture' }}</span>
        <h1>{{ $project->title }}</h1>
        <p class="detail-meta">{{ $project->location }} @if ($project->year) · {{ $project->year }}@endif</p>
    </header>

    @if ($project->children->isNotEmpty())
        @if ($project->coverImage)
            <img class="cover" src="{{ $project->coverImage->publicUrl() }}" alt="{{ $project->coverImage->alt_text ?: $project->title }}">
        @endif
        <section class="internal-project-gallery" aria-label="Internal projects"
            x-data="{ active: 0, count: {{ $project->children->count() }}, paused: false, timer: null,
                start() { if (this.count > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.timer = window.setInterval(() => { if (!this.paused && !document.hidden) this.active = (this.active + 1) % this.count }, 1000) },
                destroy() { if (this.timer) window.clearInterval(this.timer) },
                next() { this.active = (this.active + 1) % this.count },
                previous() { this.active = (this.active - 1 + this.count) % this.count } }"
            x-init="start()" x-on:mouseenter="paused = true" x-on:mouseleave="paused = false" x-on:focusin="paused = true" x-on:focusout="paused = false">
            @foreach ($project->children as $index => $child)
                @php $childCover = $child->coverImage ?: $child->images->first(); @endphp
                <a class="internal-project-slide" x-show="active === {{ $index }}" x-cloak href="{{ route('projects.show', $child->slug) }}" aria-label="View {{ $child->title }}">
                    @if ($childCover)
                        <img src="{{ $childCover->publicUrl() }}" alt="{{ $childCover->alt_text ?: $child->title }}" @if($index > 0) loading="lazy" @endif>
                    @else
                        <div class="image-placeholder"></div>
                    @endif
                    <span class="internal-slide-caption"><span>{{ $child->category?->name ?: 'Project' }}</span><strong>{{ $child->title }}</strong><span>{{ $child->location }} @if ($child->year) · {{ $child->year }} @endif</span></span>
                </a>
            @endforeach
            @if ($project->children->count() > 1)
                <div class="internal-gallery-controls">
                    <button type="button" x-on:click="previous()" aria-label="Previous internal project">‹</button>
                    <span><span x-text="active + 1"></span> / {{ $project->children->count() }}</span>
                    <button type="button" x-on:click="next()" aria-label="Next internal project">›</button>
                    <button type="button" class="pause-gallery" x-on:click="paused = !paused" x-text="paused ? 'Play' : 'Pause'">Pause</button>
                </div>
            @endif
        </section>

        <nav class="internal-project-list" aria-label="Internal projects">
            @foreach ($project->children as $child)
                <a href="{{ route('projects.show', $child->slug) }}">{{ $child->title }} <span>↗</span></a>
            @endforeach
        </nav>
        @if ($project->description)
            <div class="description prose">{{ $project->description }}</div>
        @endif
        @if ($project->images->isNotEmpty())
            <div class="gallery project-photo-grid">
                @foreach ($project->images as $index => $image)
                    <figure><img src="{{ $image->publicUrl() }}" alt="{{ $image->alt_text ?: $project->title.' '.($index + 1) }}" loading="lazy">@if($image->alt_text)<figcaption>{{ $image->alt_text }}</figcaption>@endif</figure>
                @endforeach
            </div>
        @endif
    @else
        @if ($project->description)
            <div class="description prose">{{ $project->description }}</div>
        @endif
        @if ($project->images->isNotEmpty())
            <div class="project-gallery" x-data="{ active: 0, count: {{ $project->images->count() }}, paused: false, timer: null,
                start() { if (this.count > 1 && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) this.timer = window.setInterval(() => { if (!this.paused && !document.hidden) this.active = (this.active + 1) % this.count }, 1000) },
                destroy() { if (this.timer) window.clearInterval(this.timer) },
                next() { this.active = (this.active + 1) % this.count },
                previous() { this.active = (this.active - 1 + this.count) % this.count } }"
                x-init="start()" x-on:mouseenter="paused = true" x-on:mouseleave="paused = false" x-on:focusin="paused = true" x-on:focusout="paused = false">
                <div class="gallery-main">
                    @foreach ($project->images as $index => $image)
                        <img class="gallery-focus-image" x-show="active === {{ $index }}" x-cloak src="{{ $image->publicUrl() }}" alt="{{ $image->alt_text ?: $project->title.' '.($index + 1) }}" @if($index > 0) loading="lazy" @endif>
                    @endforeach
                    @if ($project->images->count() > 1)
                        <button class="gallery-nav prev" type="button" x-on:click="previous()" aria-label="Previous image">‹</button>
                        <button class="gallery-nav next" type="button" x-on:click="next()" aria-label="Next image">›</button>
                        <button class="pause-gallery" type="button" x-on:click="paused = !paused" x-text="paused ? 'Play' : 'Pause'">Pause</button>
                    @endif
                </div>
                @if ($project->images->count() > 1)
                    <div class="gallery-thumbs">
                        @foreach ($project->images as $index => $image)
                            <button class="thumb" type="button" x-bind:class="{ 'active': active === {{ $index }} }" x-on:click="active = {{ $index }}" aria-label="View image {{ $index + 1 }}">
                                <img src="{{ $image->publicUrl() }}" alt="">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    @endif
</article>
