<a class="project-card" href="{{ route('projects.show', $project->slug) }}">
    <div class="project-image">
        @php $cover = $project->coverImage ?: $project->images->first(); @endphp
        @if ($cover)
            <img src="{{ $cover->publicUrl() }}" alt="{{ $cover->alt_text ?: $project->title }}" loading="lazy">
        @else
            <div class="image-placeholder" aria-hidden="true"></div>
        @endif
        @if (($project->children_count ?? 0) > 0)
            <span class="subproject-count">{{ $project->children_count }} @if(isset($homepageCopy))<span data-en="{{ $homepageCopy['internal_projects_suffix']['en'] }}" data-ar="{{ $homepageCopy['internal_projects_suffix']['ar'] }}">{{ $homepageCopy['internal_projects_suffix']['en'] }}</span>@else internal projects @endif</span>
        @endif
    </div>
    <div class="project-meta">
        <h3>{{ $project->title }}</h3>
        <p>@if($project->category){{ $project->category->name }}@elseif(isset($homepageCopy))<span data-en="{{ $homepageCopy['project_category_fallback']['en'] }}" data-ar="{{ $homepageCopy['project_category_fallback']['ar'] }}">{{ $homepageCopy['project_category_fallback']['en'] }}</span>@else Architecture @endif <i></i> @if($project->location){{ $project->location }}@elseif(isset($homepageCopy))<span data-en="{{ $homepageCopy['project_location_fallback']['en'] }}" data-ar="{{ $homepageCopy['project_location_fallback']['ar'] }}">{{ $homepageCopy['project_location_fallback']['en'] }}</span>@else Cairo @endif @if ($project->year)<i></i> {{ $project->year }}@endif</p>
    </div>
</a>
