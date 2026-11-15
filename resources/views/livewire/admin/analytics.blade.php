<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Reports</span><h1>Analytics</h1><p>Current totals from the existing project, inquiry, and page-view data.</p></div></div>
    <div class="admin-stats-grid">
        @foreach (['Main projects' => $totalProjects, 'Internal projects' => $totalSubprojects, 'Categories' => $totalCategories, 'Inquiries' => $inquiriesCount, 'Page views' => $pageViewsCount, 'Stored files' => $storageFiles] as $label => $value)
            <article class="admin-stat"><span>{{ $label }}</span><strong>{{ number_format($value) }}</strong></article>
        @endforeach
    </div>
    <p class="admin-footnote">Stored files occupy {{ number_format($storageBytes / 1024 / 1024, 2) }} MB.</p>
</section>
