<section class="admin-page project-manager-page">
    <div class="admin-page-heading project-manager-heading">
        <div><span class="eyebrow">Portfolio content</span><h1>Projects &amp; subprojects</h1><p>Build a clear project tree: each main project can hold any number of detailed subprojects, each with its own description and gallery.</p></div>
        <button class="button" type="button" wire:click="resetForm">＋ New project</button>
    </div>

    <div class="project-admin-grid">
        <form class="admin-panel admin-form project-editor" wire:submit="save" enctype="multipart/form-data">
            <header class="project-editor-heading">
                <span class="eyebrow">{{ $parentId ? 'Project inside a main project' : 'Portfolio entry' }}</span>
                <h2>{{ $editingId ? 'Edit project' : ($parentId ? 'Add subproject' : 'Add main project') }}</h2>
                <p>{{ $parentId ? 'This subproject inherits its main project’s category.' : 'Start with a main project, then add its subprojects and image galleries.' }}</p>
            </header>

            @if ($askAddAnotherSubproject)
                <section class="subproject-next-prompt" role="status">
                    <span class="subproject-prompt-check" aria-hidden="true">✓</span>
                    <div><strong>Subproject saved</strong><p>Add another subproject under <b>{{ $lastSavedSubprojectParent }}</b>?</p></div>
                    <div class="subproject-prompt-actions">
                        <button class="button" type="button" wire:click="continueAddingSubproject">Yes, add another</button>
                        <button class="button secondary" type="button" wire:click="finishAddingSubprojects">No, finish</button>
                    </div>
                </section>
            @else
                <div class="project-form-fields">
                    <div class="admin-field"><label for="project-title">Project name <span>*</span></label><input id="project-title" wire:model="title" autocomplete="off" required placeholder="e.g. Riverside Residence"><small>A short, descriptive title shown in the portfolio.</small>@error('title')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                    <div class="admin-field"><label for="project-slug">Page address</label><input id="project-slug" wire:model="slug" autocomplete="off" placeholder="Created from the project name">@error('slug')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>

                    <div class="admin-field"><label for="project-parent">Main project <small>(optional)</small></label><select id="project-parent" wire:model.live="parentId"><option value="">This is a main project</option>@foreach($parentProjects as $parentProject)<option value="{{ $parentProject->id }}">{{ $parentProject->title }}</option>@endforeach</select><small>Choose a main project to add this as one of its subprojects.</small>@error('parentId')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>

                    @if (!$parentId)
                        <div class="admin-field"><label for="project-category">Category <span>*</span></label><select id="project-category" wire:model="categoryId"><option value="">Choose a category</option>@foreach($categories as $category)<option value="{{ $category->id }}">{{ $category->name }}</option>@endforeach</select><small>Subprojects inherit this category automatically.</small>@error('categoryId')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                    @else
                        <div class="inherited-category-note"><span>Category inherited from</span><strong>{{ $selectedParent?->title ?? 'Main project' }}</strong><small>{{ $selectedParent?->category?->name ?? 'No category assigned to the main project' }}</small></div>
                    @endif

                    <div class="admin-field"><label for="project-description">Description <span>*</span></label><textarea id="project-description" wire:model="description" rows="5" required placeholder="Describe the concept, materials, spatial approach, and project context…"></textarea>@error('description')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>

                    <div class="form-grid project-detail-fields">
                        <div class="admin-field"><label for="project-location">Location</label><input id="project-location" wire:model="location" placeholder="City, country">@error('location')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                        <div class="admin-field"><label for="project-year">Year</label><input id="project-year" type="number" min="1800" max="2200" wire:model="year" placeholder="2025">@error('year')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                        <div class="admin-field"><label for="project-order">Display order</label><input id="project-order" type="number" min="0" wire:model="sortOrder">@error('sortOrder')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                    </div>

                    @if ($existingImages)
                        <fieldset class="image-manager project-image-manager"><legend>Project gallery</legend><p class="field-hint">Choose a cover image and keep the remaining images in the gallery.</p><div class="image-manager-grid">
                            @foreach($existingImages as $image)
                                <div class="image-manager-item"><img src="{{ $image['url'] }}" alt="{{ $image['alt_text'] ?: $title }}" loading="lazy"><label class="checkbox-row"><input type="radio" name="coverImageId" value="{{ $image['id'] }}" wire:click="setCover({{ $image['id'] }})" @checked($image['is_cover'])> Cover image</label><button class="danger-link" type="button" wire:click="removeImage({{ $image['id'] }})" wire:confirm="Remove this image permanently?">Remove image</button></div>
                            @endforeach
                        </div></fieldset>
                    @endif

                    <div class="admin-field upload-field"><label for="project-images">{{ $existingImages ? 'Add gallery images' : 'Upload project images' }}</label><input id="project-images" type="file" wire:model="imageUploads" accept="image/jpeg,image/png,image/webp" multiple><small>JPG, PNG or WebP · up to 20 images · 8 MB each</small>@error('imageUploads')<span class="field-error" role="alert">{{ $message }}</span>@enderror @error('imageUploads.*')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                    @if($imageUploads)
                        <fieldset class="image-manager project-image-manager"><legend>New images</legend><div class="admin-field"><label for="cover-upload">Cover image</label><select id="cover-upload" wire:model="coverUploadIndex"><option value="">Keep the current cover</option>@foreach($imageUploads as $index => $image)<option value="{{ $index }}">New image {{ $index + 1 }}</option>@endforeach</select></div><div class="image-manager-grid">@foreach($imageUploads as $index => $image)<div class="image-manager-item">@if($image->isPreviewable())<img src="{{ $image->temporaryUrl() }}" alt="New project image preview">@endif<span>Image {{ $index + 1 }}</span></div>@endforeach</div></fieldset>
                    @endif
                    <div wire:loading wire:target="imageUploads" class="loading-copy">Uploading and preparing images…</div>
                    <div class="form-actions project-form-actions"><button class="button" type="submit" wire:loading.attr="disabled">{{ $editingId ? 'Save changes' : ($parentId ? 'Save subproject' : 'Save main project') }}</button>@if($editingId)<button class="button secondary" type="button" wire:click="resetForm">Cancel</button>@endif</div>
                </div>
            @endif
        </form>

        <section class="admin-panel project-library">
            <div class="project-library-heading"><div><span class="eyebrow">Portfolio structure</span><h2>Project library</h2><p>Main projects and their subprojects in one place.</p></div></div>
            <div class="table-tools project-library-tools"><div><label for="project-status">Show</label><select id="project-status" wire:model.live="status"><option value="active">Published</option><option value="archived">Archived</option></select></div><input type="search" wire:model.live.debounce.300ms="search" placeholder="Find a project…" aria-label="Search projects"></div>
            <div class="admin-table-wrap"><table class="admin-table project-library-table"><thead><tr><th>Project</th><th>Category</th><th>Year</th><th>Actions</th></tr></thead><tbody>
                @forelse ($projects as $project)
                    <tr @class(['project-child-row' => $project->parent])><td><div class="project-row-name">@if($project->parent)<span class="project-tree-branch" aria-hidden="true">↳</span>@else<span class="project-tree-root" aria-hidden="true">▰</span>@endif<div><strong>{{ $project->title }}</strong>@if($project->parent)<small>Subproject · {{ $project->parent->title }}</small>@elseif($project->children->isNotEmpty())<small>{{ $project->children->count() }} subprojects</small>@else<small>Main project</small>@endif</div></div></td><td>{{ $project->parent?->category?->name ?: $project->category?->name ?: '—' }}</td><td>{{ $project->year ?: '—' }}</td><td class="table-actions">
                        @if(!$project->trashed())<button type="button" wire:click="edit({{ $project->id }})">Edit</button><a href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener">Preview</a><button class="danger-link" type="button" wire:click="archive({{ $project->id }})" wire:confirm="Archive this project? It will be hidden from the public site and can be restored.">Archive</button>
                        @else<button type="button" wire:click="restore({{ $project->id }})">Restore</button><button class="danger-link" type="button" wire:click="forceDelete({{ $project->id }})" wire:confirm="Permanently delete this archived project, its subprojects, and images? This cannot be undone.">Delete permanently</button>@endif
                    </td></tr>
                @empty
                    <tr><td colspan="4" class="empty-state">No projects found. Add a main project to begin.</td></tr>
                @endforelse
            </tbody></table></div>
            {{ $projects->links() }}
        </section>
    </div>
</section>