<section class="admin-page category-manager-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Organization</span><h1>Categories</h1><p>Give every category a clear name and cover image. These images appear as visual filters on the homepage.</p></div></div>
    <div class="admin-columns">
        <form class="admin-panel admin-form category-editor" wire:submit="save" enctype="multipart/form-data">
            @error('database')<div class="field-error" role="alert">{{ $message }}</div>@enderror
            <header><span class="eyebrow">Portfolio taxonomy</span><h2>{{ $editingId ? 'Edit category' : 'New category' }}</h2></header>
            <div class="admin-field"><label for="category-name">Name <span>*</span></label><input id="category-name" wire:model="name" required placeholder="Residential"><small>Use the name visitors will recognize.</small>@error('name')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="category-slug">Page address</label><input id="category-slug" wire:model="slug" placeholder="Created from the name">@error('slug')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
            <div class="admin-field"><label for="category-description">Short description</label><textarea id="category-description" wire:model="description" rows="3" placeholder="A brief note about this type of work."></textarea>@error('description')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
            <div class="admin-field category-image-field"><label for="category-image">Category cover image</label>@if($currentImageUrl)<img class="category-image-preview" src="{{ $currentImageUrl }}" alt="Current category cover">@endif<input id="category-image" type="file" wire:model="categoryImage" accept="image/jpeg,image/png,image/webp"><small>JPG, PNG or WebP · up to 4 MB. If omitted, a project cover is used as a fallback.</small>@error('categoryImage')<span class="field-error" role="alert">{{ $message }}</span>@enderror @if($categoryImage && $categoryImage->isPreviewable())<img class="category-image-preview" src="{{ $categoryImage->temporaryUrl() }}" alt="New category cover preview">@endif</div>
            <div class="form-grid"><div class="admin-field"><label for="category-sort">Display order</label><input id="category-sort" type="number" min="0" wire:model="sortOrder">@error('sortOrder')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div><label class="checkbox-row category-visibility"><input type="checkbox" wire:model="isActive"> Visible on the public site</label></div>
            <div wire:loading wire:target="categoryImage" class="loading-copy">Uploading category image…</div>
            <div class="form-actions"><button class="button" type="submit" wire:loading.attr="disabled">{{ $editingId ? 'Save category' : 'Add category' }}</button>@if($editingId)<button class="button secondary" type="button" wire:click="resetForm">Cancel</button>@endif</div>
        </form>

        <section class="admin-panel category-library">
            <div class="table-tools"><div><span class="eyebrow">Category library</span><h2>All categories</h2></div><input type="search" wire:model.live.debounce.300ms="search" placeholder="Find a category…" aria-label="Search categories"></div>
            <div class="admin-table-wrap"><table class="admin-table category-library-table"><thead><tr><th>Category</th><th>Projects</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                @forelse ($categories as $category)
                    <tr><td><div class="category-admin-row">@if($category->image_path)<img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($category->image_path) }}" alt="" loading="lazy">@else<span class="category-admin-placeholder">{{ mb_strtoupper(mb_substr($category->name, 0, 1)) }}</span>@endif<div><strong>{{ $category->name }}</strong><small>{{ $category->slug }}</small></div></div></td><td>{{ $category->projects_count }}</td><td><span class="status-pill {{ $category->is_active ? 'is-visible' : 'is-hidden' }}">{{ $category->is_active ? 'Visible' : 'Hidden' }}</span></td><td class="table-actions"><button type="button" wire:click="edit({{ $category->id }})">Edit</button><button type="button" class="danger-link" wire:click="delete({{ $category->id }})" wire:confirm="Delete this category? Projects will remain without a category.">Delete</button></td></tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No categories found.</td></tr>
                @endforelse
            </tbody></table></div>
            {{ $categories->links() }}
        </section>
    </div>
</section>
