<section class="section portfolio-page">
    <div class="section-head">
        <div><span class="eyebrow">Portfolio</span><h1>Projects</h1><p>A selection of spatial, residential and cultural work.</p></div>
    </div>

    @if ($categories->isNotEmpty())
        <div class="filter-bar">
            <label class="eyebrow" for="projects-category">Filter</label>
            <select id="projects-category" wire:model.live="category" aria-label="Filter projects by category">
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
