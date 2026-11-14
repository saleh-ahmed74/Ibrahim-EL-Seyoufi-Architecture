@php
    $firstPortfolioProject = $projects->first();
    $firstPortfolioImage = $firstPortfolioProject?->coverImage ?: $firstPortfolioProject?->images->first();
    $allWorkImage = $settings->hero_image ? $settings->publicImageUrl($settings->hero_image) : $firstPortfolioImage?->publicUrl();
    $activeCategoryName = $categories->firstWhere('slug', $selectedCategory)?->name;
@endphp
<div class="category-filter-block" x-data="{ categoriesOpen: false }">
    <div class="category-filter-heading"><div><span class="eyebrow" data-en="Explore by discipline" data-ar="استكشف حسب التخصص">Explore by discipline</span><p data-en="Choose a category to refine the portfolio." data-ar="اختر تصنيفًا لعرض المشاريع المرتبطة به.">Choose a category to refine the portfolio.</p></div>
        <button class="category-filter-toggle" type="button" aria-controls="category-filter-options" x-bind:aria-expanded="categoriesOpen.toString()" x-on:click="categoriesOpen = !categoriesOpen">
            <span data-en="Categories" data-ar="التصنيفات">Categories</span>
            <strong>@if($selectedCategory){{ $activeCategoryName ?: 'Selected category' }}@else<span data-en="All projects" data-ar="كل المشاريع">All projects</span>@endif</strong>
            <svg viewBox="0 0 16 16" aria-hidden="true"><path d="m3 6 5 5 5-5"/></svg>
        </button>
    </div>
    <div id="category-filter-options" class="category-filter-rail" role="group" aria-label="Filter projects by category" x-bind:class="categoriesOpen ? 'is-open' : ''">
        <button type="button" class="category-filter-card {{ $selectedCategory === '' ? 'is-active' : '' }}" wire:click="filterByCategory('')" x-on:click="categoriesOpen = false" aria-pressed="{{ $selectedCategory === '' ? 'true' : 'false' }}">
            <span class="category-filter-image">@if($allWorkImage)<img src="{{ $allWorkImage }}" alt="" loading="lazy">@else<span class="category-filter-placeholder">All</span>@endif</span>
            <span class="category-filter-copy"><strong data-en="All projects" data-ar="كل المشاريع">All projects</strong><small>Explore all work</small></span>
        </button>
        @foreach ($categories as $categoryOption)
            @php $categoryImage = $categoryOption->displayImageUrl() ?: $allWorkImage; @endphp
            <button type="button" class="category-filter-card {{ $selectedCategory === $categoryOption->slug ? 'is-active' : '' }}" wire:key="category-filter-{{ $categoryOption->id }}" wire:click="filterByCategory('{{ $categoryOption->slug }}')" x-on:click="categoriesOpen = false" aria-pressed="{{ $selectedCategory === $categoryOption->slug ? 'true' : 'false' }}">
                <span class="category-filter-image">@if($categoryImage)<img src="{{ $categoryImage }}" alt="" loading="lazy">@else<span class="category-filter-placeholder">{{ mb_strtoupper(mb_substr($categoryOption->name, 0, 1)) }}</span>@endif</span>
                <span class="category-filter-copy"><strong>{{ $categoryOption->name }}</strong><small>{{ $categoryOption->main_projects_count ?? 0 }} {{ ($categoryOption->main_projects_count ?? 0) === 1 ? 'project' : 'projects' }}</small></span>
            </button>
        @endforeach
    </div>
</div>