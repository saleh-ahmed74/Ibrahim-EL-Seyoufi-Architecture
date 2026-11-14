<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Overview</span><h1>Dashboard</h1></div></div>
    <div class="admin-stats-grid">
        @foreach (['Projects' => $totalProjects, 'Internal projects' => $totalSubprojects, 'Categories' => $totalCategories, 'Inquiries' => $inquiriesCount, 'Page views' => $pageViewsCount, 'Stored images' => $storageFiles] as $label => $value)
            <article class="admin-stat"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></article>
        @endforeach
    </div>
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
