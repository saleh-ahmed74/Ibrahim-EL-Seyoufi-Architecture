<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Organization</span><h1>Categories</h1></div></div>

    <div class="admin-columns">
        <form class="admin-panel admin-form" wire:submit="save">
            <h2>{{ $editingId ? 'Edit category' : 'New category' }}</h2>
            <label for="category-name">Name</label><input id="category-name" wire:model="name" required>
            @error('name')<span class="field-error">{{ $message }}</span>@enderror
            <label for="category-slug">Slug</label><input id="category-slug" wire:model="slug" placeholder="Generated from the name">
            @error('slug')<span class="field-error">{{ $message }}</span>@enderror
            <label for="category-description">Description</label><textarea id="category-description" wire:model="description" rows="4"></textarea>
            @error('description')<span class="field-error">{{ $message }}</span>@enderror
            <label for="category-sort">Display order</label><input id="category-sort" type="number" min="0" wire:model="sortOrder">
            @error('sortOrder')<span class="field-error">{{ $message }}</span>@enderror
            <label class="checkbox-row"><input type="checkbox" wire:model="isActive"> Visible on the public site</label>
            <div class="form-actions"><button class="button" type="submit" wire:loading.attr="disabled">Save category</button>@if($editingId)<button class="button secondary" type="button" wire:click="resetForm">Cancel</button>@endif</div>
        </form>

        <div class="admin-panel">
            <div class="table-tools"><h2>Categories</h2><input type="search" wire:model.live.debounce.300ms="search" placeholder="Search categories"></div>
            <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Name</th><th>Slug</th><th>Projects</th><th>Status</th><th>Actions</th></tr></thead><tbody>
                @forelse ($categories as $category)
                    <tr><td>{{ $category->name }}</td><td><code>{{ $category->slug }}</code></td><td>{{ $category->projects_count }}</td><td>{{ $category->is_active ? 'Visible' : 'Hidden' }}</td><td class="table-actions"><button type="button" wire:click="edit({{ $category->id }})">Edit</button><button type="button" class="danger-link" wire:click="delete({{ $category->id }})" wire:confirm="Delete this category? Projects will remain and become uncategorized.">Delete</button></td></tr>
                @empty
                    <tr><td colspan="5" class="empty-state">No categories found.</td></tr>
                @endforelse
            </tbody></table></div>
            {{ $categories->links() }}
        </div>
    </div>
</section>
