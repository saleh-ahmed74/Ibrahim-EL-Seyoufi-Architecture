<section class="section portfolio-page">
    <div class="section-head">
        <div><span class="eyebrow">Portfolio</span><h1>Projects</h1><p>A selection of spatial, residential and cultural work.</p></div>
    </div>

    @include('livewire.site.category-filter', ['categories' => $categories, 'projects' => $projects, 'settings' => $settings, 'selectedCategory' => $category])

    <div class="projects-grid">
        @forelse ($projects as $project)
            @include('livewire.site.project-card', ['project' => $project])
        @empty
            <p class="empty-state">No projects are available in this category yet.</p>
        @endforelse
    </div>
</section>
