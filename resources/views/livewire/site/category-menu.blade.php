<div class="site-category-menu" x-data="{ open: false }" x-on:click.outside="open = false" x-on:keydown.escape.window="open = false">
    <button class="site-category-menu-toggle" type="button" aria-haspopup="true" aria-controls="site-category-menu-options" x-bind:aria-expanded="open.toString()" x-on:click="open = !open; if (open && !$wire.loaded) $wire.loadCategories()">
        <span data-en="{{ $copy['categories_link']['en'] }}" data-ar="{{ $copy['categories_link']['ar'] }}">{{ $copy['categories_link']['en'] }}</span>
        <svg viewBox="0 0 16 16" aria-hidden="true"><path d="m3 6 5 5 5-5"/></svg>
    </button>
    <div id="site-category-menu-options" class="site-category-menu-options" x-show="open" x-transition x-cloak>
        <a href="{{ route('projects.index') }}" data-en="{{ $copy['all_projects']['en'] }}" data-ar="{{ $copy['all_projects']['ar'] }}">{{ $copy['all_projects']['en'] }}</a>
        @if($loaded)
            @forelse($categories as $category)
                <a href="{{ route('projects.index', ['category' => $category['slug']]) }}">{{ $category['name'] }}</a>
            @empty
                <span class="site-category-menu-empty" data-en="{{ $copy['no_categories']['en'] }}" data-ar="{{ $copy['no_categories']['ar'] }}">{{ $copy['no_categories']['en'] }}</span>
            @endforelse
        @else
            <span class="site-category-menu-empty" data-en="{{ $copy['loading_categories']['en'] }}" data-ar="{{ $copy['loading_categories']['ar'] }}">{{ $copy['loading_categories']['en'] }}</span>
        @endif
    </div>
</div>
