<a class="project-card" href="{{ route('projects.show', $project->slug) }}">
    <div class="project-image">
        @php $cover = $project->coverImage ?: $project->images->first(); @endphp
        @if ($cover)
            <img src="{{ $cover->publicUrl() }}" alt="{{ $cover->alt_text ?: $project->title }}" loading="lazy">
        @else
            <div class="image-placeholder" aria-hidden="true"></div>
        @endif
        @if ($project->children->isNotEmpty())
            <span class="subproject-count">{{ $project->children->count() }} internal projects</span>
        @endif
    </div>
    <div class="project-meta">
        <h3>{{ $project->title }}</h3>
        <p>{{ $project->category?->name ?: 'Architecture' }} <i></i> {{ $project->location ?: 'Cairo' }} @if ($project->year)<i></i> {{ $project->year }}@endif</p>
    </div>
</a>
