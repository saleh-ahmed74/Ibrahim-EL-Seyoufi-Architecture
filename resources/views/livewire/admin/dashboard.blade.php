<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Overview</span><h1>Dashboard</h1></div></div>
    <div class="admin-stats-grid">
        @foreach (['Projects' => $totalProjects, 'Internal projects' => $totalSubprojects, 'Categories' => $totalCategories, 'Inquiries' => $inquiriesCount, 'Page views' => $pageViewsCount, 'Stored images' => $storageFiles] as $label => $value)
            <article class="admin-stat"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></article>
        @endforeach
    </div>
    <section class="admin-panel dashboard-projects-panel">
        <header class="dashboard-projects-heading"><div><span class="eyebrow">Portfolio</span><h2>Main projects</h2><p>Open the project manager to edit a project or expand its subprojects.</p></div><a class="button" href="{{ route('admin.projects') }}">Open project manager</a></header>
        <div class="dashboard-project-list">
            @forelse($portfolioProjects as $project)
                @php $dashboardCover = $project->coverImage ?: $project->images->first(); @endphp
                <a class="dashboard-project-item" href="{{ route('admin.projects') }}">
                    <span class="dashboard-project-cover">@if($dashboardCover)<img src="{{ $dashboardCover->publicUrl() }}" alt="" loading="lazy">@else<span class="image-placeholder"></span>@endif</span>
                    <span class="dashboard-project-copy"><strong>{{ $project->title }}</strong><small>{{ $project->category?->name ?: 'Architecture' }}@if($project->location) · {{ $project->location }}@endif @if($project->year) · {{ $project->year }}@endif</small></span>
                    <span class="project-count-pill">{{ $project->children_count }} {{ $project->children_count === 1 ? 'subproject' : 'subprojects' }}</span><span class="dashboard-project-arrow" aria-hidden="true">↗</span>
                </a>
            @empty
                <p class="empty-state">No projects yet. Open the project manager to add the first one.</p>
            @endforelse
        </div>
    </section>
    <section class="admin-panel">
        <h2>Projects by category</h2>
        @forelse ($projectsByCategory as $row)
            <div class="bar-row"><span>{{ $row['name'] }}</span><span class="bar-track"><i style="width: {{ $totalProjects ? min(100, ($row['count'] / $totalProjects) * 100) : 0 }}%"></i></span><strong>{{ $row['count'] }}</strong></div>
        @empty
            <p class="empty-state">No category data yet.</p>
        @endforelse
    </section>
    <section class="admin-panel">
        <h2>Projects by year</h2>
        @forelse ($projectsByYear as $row)
            <div class="bar-row"><span>{{ $row['year'] }}</span><span class="bar-track"><i style="width: {{ $projectsByYear ? min(100, ($row['count'] / max(array_column($projectsByYear, 'count'))) * 100) : 0 }}%"></i></span><strong>{{ $row['count'] }}</strong></div>
        @empty
            <p class="empty-state">Add years to projects to see this summary.</p>
        @endforelse
    </section>
    <p class="admin-footnote">Stored image size: {{ number_format($storageBytes / 1024 / 1024, 2) }} MB</p>
</section>
