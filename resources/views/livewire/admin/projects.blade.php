<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Portfolio content</span><h1>Projects</h1><p>Main projects can contain internal projects. All records continue to use the existing projects table.</p></div></div>

    <div class="project-admin-grid">
        <form class="admin-panel admin-form" wire:submit="save" enctype="multipart/form-data">
            <h2>{{ $editingId ? 'Edit project' : 'New project' }}</h2>
            <label for="project-title">Title</label><input id="project-title" wire:model="title" required>
            @error('title')<span class="field-error">{{ $message }}</span>@enderror
            <label for="project-slug">Slug</label><input id="project-slug" wire:model="slug" placeholder="Generated from the title">
            @error('slug')<span class="field-error">{{ $message }}</span>@enderror
            <label for="project-parent">Main project</label>
            <select id="project-parent" wire:model="parentId"><option value="">Main project</option>@foreach($parentProjects as $parentProject)<option value="{{ $parentProject->id }}">{{ $parentProject->title }}</option>@endforeach</select>
            @error('parentId')<span class="field-error">{{ $message }}</span>@enderror
            <label for="project-category">Category</label><select id="project-category" wire:model="categoryId"><option value="">Uncategorized</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select>
            @error('categoryId')<span class="field-error">{{ $message }}</span>@enderror
            <label for="project-description">Description</label><textarea id="project-description" wire:model="description" rows="6" required></textarea>
            @error('description')<span class="field-error">{{ $message }}</span>@enderror
            <div class="form-grid">
                <div><label for="project-location">Location</label><input id="project-location" wire:model="location">@error('location')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div><label for="project-year">Year</label><input id="project-year" type="number" min="1800" max="2200" wire:model="year">@error('year')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div><label for="project-order">Display order</label><input id="project-order" type="number" min="0" wire:model="sortOrder">@error('sortOrder')<span class="field-error">{{ $message }}</span>@enderror</div>
            </div>

            @if ($existingImages)
                <fieldset class="image-manager"><legend>Current images</legend><div class="image-manager-grid">
                    @foreach($existingImages as $image)
                        <div class="image-manager-item"><img src="{{ $image['url'] }}" alt="{{ $image['alt_text'] ?: $title }}" loading="lazy"><label class="checkbox-row"><input type="radio" name="coverImageId" value="{{ $image['id'] }}" wire:click="setCover({{ $image['id'] }})" @checked($image['is_cover'])> Cover image</label><button class="danger-link" type="button" wire:click="removeImage({{ $image['id'] }})" wire:confirm="Remove this image permanently?">Remove</button></div>
                    @endforeach
                </div></fieldset>
            @endif
            <label for="project-images">Add images (JPG, PNG or WebP, up to 8 MB each)</label>
            <input id="project-images" type="file" wire:model="imageUploads" accept="image/jpeg,image/png,image/webp" multiple>
            @error('imageUploads')<span class="field-error">{{ $message }}</span>@enderror
            @error('imageUploads.*')<span class="field-error">{{ $message }}</span>@enderror
            @if($imageUploads)
                <fieldset class="image-manager"><legend>New uploads</legend>
                    <label for="cover-upload">Cover image</label><select id="cover-upload" wire:model="coverUploadIndex"><option value="">Keep the current cover</option>@foreach($imageUploads as $index => $image)<option value="{{ $index }}">New image {{ $index + 1 }}</option>@endforeach</select>
                    <div class="image-manager-grid">@foreach($imageUploads as $index => $image)<div class="image-manager-item">@if($image->isPreviewable())<img src="{{ $image->temporaryUrl() }}" alt="New project image preview">@endif<span>New image {{ $index + 1 }}</span></div>@endforeach</div>
                </fieldset>
            @endif
            <div wire:loading wire:target="imageUploads" class="loading-copy">Uploading images…</div>
            <div class="form-actions"><button class="button" type="submit" wire:loading.attr="disabled">Save project</button>@if($editingId)<button class="button secondary" type="button" wire:click="resetForm">Cancel</button>@endif</div>
        </form>

        <div class="admin-panel">
            <div class="table-tools"><div><h2>Projects</h2><label for="project-status">Status</label> <select id="project-status" wire:model.live="status"><option value="active">Active</option><option value="archived">Archived</option></select></div><input type="search" wire:model.live.debounce.300ms="search" placeholder="Search projects"></div>
            <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Project</th><th>Category</th><th>Year</th><th>Actions</th></tr></thead><tbody>
                @forelse ($projects as $project)
                    <tr><td><strong>{{ $project->title }}</strong>@if($project->parent)<small>Inside {{ $project->parent->title }}</small>@elseif($project->children->isNotEmpty())<small>{{ $project->children->count() }} internal projects</small>@endif</td><td>{{ $project->category?->name ?: '—' }}</td><td>{{ $project->year ?: '—' }}</td><td class="table-actions">
                        @if(!$project->trashed())<button type="button" wire:click="edit({{ $project->id }})">Edit</button><a href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener">View</a><button class="danger-link" type="button" wire:click="archive({{ $project->id }})" wire:confirm="Archive this project? It will be hidden from the public site and can be restored.">Archive</button>
                        @else<button type="button" wire:click="restore({{ $project->id }})">Restore</button><button class="danger-link" type="button" wire:click="forceDelete({{ $project->id }})" wire:confirm="Permanently delete this archived project, its internal projects, and images? This cannot be undone.">Delete permanently</button>@endif
                    </td></tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No projects found.</td></tr>
                @endforelse
            </tbody></table></div>
            {{ $projects->links() }}
        </div>
    </div>
</section>
