<section class="admin-page project-manager-page" x-data="{}" x-on:focus-project-editor.window="$nextTick(() => { const editor = $el.querySelector('#project-editor'); editor?.scrollIntoView({behavior: 'smooth', block: 'center'}); editor?.querySelector('#project-title')?.focus({preventScroll: true}) })">
    <div class="admin-page-heading project-manager-heading">
        <div><span class="eyebrow">Portfolio content</span><h1>Projects &amp; subprojects</h1><p>Each main project contains its own subprojects. Open a project below to view or manage its subprojects.</p></div>
        <button class="button" type="button" wire:click="resetForm">＋ New main project</button>
    </div>

    <div class="project-admin-grid">
        <form id="project-editor" class="admin-panel admin-form project-editor" wire:submit="save" enctype="multipart/form-data" tabindex="-1">
            <header class="project-editor-heading">
                <span class="eyebrow">{{ $parentId ? 'Project inside a main project' : 'Portfolio entry' }}</span>
                <h2>{{ $editingId ? 'Edit project' : ($parentId ? 'Add subproject' : 'Add main project') }}</h2>
                <p>{{ $parentId ? 'This subproject inherits its main project’s category.' : 'Add a main project and its gallery. Add subprojects from each project card.' }}</p>
            </header>

            @if ($askAddAnotherSubproject)
                <section class="subproject-next-prompt" role="status">
                    <span class="subproject-prompt-check" aria-hidden="true">✓</span>
                    <div><strong>Subproject saved</strong><p>Add another subproject under <b>{{ $lastSavedSubprojectParent }}</b>?</p></div>
                    <div class="subproject-prompt-actions"><button class="button" type="button" wire:click="continueAddingSubproject">Yes, add another</button><button class="button secondary" type="button" wire:click="finishAddingSubprojects">No, finish</button></div>
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
                    <div class="form-grid project-detail-fields"><div class="admin-field"><label for="project-location">Location</label><input id="project-location" wire:model="location" placeholder="City, country">@error('location')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div><div class="admin-field"><label for="project-year">Year</label><input id="project-year" type="number" min="1800" max="2200" wire:model="year" placeholder="2025">@error('year')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div></div>

                    @if ($existingImages)
                        <fieldset class="image-manager project-image-manager"><legend>Current project images</legend><p class="field-hint">Edit the main project gallery here. Remove an image or choose a new cover.</p><div class="image-manager-grid">
                            @foreach($existingImages as $image)
                                <div class="image-manager-item"><img src="{{ $image['url'] }}" alt="{{ $image['alt_text'] ?: $title }}" loading="lazy"><label class="checkbox-row"><input type="radio" name="coverImageId" value="{{ $image['id'] }}" wire:click="setCover({{ $image['id'] }})" @checked($image['is_cover'])> Cover image</label><button class="danger-link" type="button" wire:click="removeImage({{ $image['id'] }})" wire:confirm="Remove this image permanently?">Remove image</button></div>
                            @endforeach
                        </div></fieldset>
                    @endif
                    <div class="admin-field upload-field"><label for="project-images">{{ $existingImages ? 'Add gallery images' : 'Upload project images' }}</label><input id="project-images" type="file" wire:model="imageUploads" accept="image/jpeg,image/png,image/webp" multiple><small>JPG, PNG or WebP · up to 20 images · 8 MB each</small>@error('imageUploads')<span class="field-error" role="alert">{{ $message }}</span>@enderror @error('imageUploads.*')<span class="field-error" role="alert">{{ $message }}</span>@enderror</div>
                    @if($imageUploads)<fieldset class="image-manager project-image-manager"><legend>New images</legend><div class="admin-field"><label for="cover-upload">Cover image</label><select id="cover-upload" wire:model="coverUploadIndex"><option value="">Keep the current cover</option>@foreach($imageUploads as $index => $image)<option value="{{ $index }}">New image {{ $index + 1 }}</option>@endforeach</select></div><div class="image-manager-grid">@foreach($imageUploads as $index => $image)<div class="image-manager-item">@if($image->isPreviewable())<img src="{{ $image->temporaryUrl() }}" alt="New project image preview">@endif<span>Image {{ $index + 1 }}</span></div>@endforeach</div></fieldset>@endif
                    <div wire:loading wire:target="imageUploads" class="loading-copy">Uploading and preparing images…</div>
                    <div class="form-actions project-form-actions"><button class="button" type="submit" wire:loading.attr="disabled">{{ $editingId ? 'Save changes' : ($parentId ? 'Save subproject' : 'Save main project') }}</button>@if($editingId)<button class="button secondary" type="button" wire:click="resetForm">Cancel</button>@endif</div>
                </div>
            @endif
        </form>

        <section class="admin-panel project-library">
            <div class="project-library-heading"><div><span class="eyebrow">Portfolio structure</span><h2>{{ $status === 'archived' ? 'Archived projects' : 'Main projects' }}</h2><p>{{ $status === 'archived' ? 'Restore a project or remove it permanently.' : 'Subprojects stay tucked under their main project until you open it.' }}</p></div></div>
            <div class="table-tools project-library-tools"><div><label for="project-status">Show</label><select id="project-status" wire:model.live="status"><option value="active">Published</option><option value="archived">Archived</option></select></div><input type="search" wire:model.live.debounce.300ms="search" placeholder="Find a project…" aria-label="Search projects"></div>

            @if($status === 'archived')
                <div class="admin-table-wrap"><table class="admin-table project-library-table"><thead><tr><th>Archived project</th><th>Type</th><th>Actions</th></tr></thead><tbody>
                    @forelse($projects as $project)
                        <tr><td><strong>{{ $project->title }}</strong>@if($project->parent)<small>Subproject of {{ $project->parent->title }}</small>@endif</td><td>{{ $project->parent ? 'Subproject' : 'Main project' }}</td><td class="table-actions"><button type="button" wire:click="restore({{ $project->id }})">Restore project</button><button class="danger-link" type="button" wire:click="forceDelete({{ $project->id }})" wire:confirm="Permanently delete this project and its images? This cannot be undone.">Permanently delete</button></td></tr>
                    @empty<tr><td colspan="3" class="empty-state">No archived projects.</td></tr>@endforelse
                </tbody></table></div>
            @else
                <div class="admin-main-project-list">
                    @forelse($projects as $project)
                        @php $mainCover = $project->coverImage ?: $project->images->first(); @endphp
                        <article class="admin-main-project-card" wire:key="main-project-{{ $project->id }}">
                            <div class="admin-main-project-cover">@if($mainCover)<img src="{{ $mainCover->publicUrl() }}" alt="{{ $mainCover->alt_text ?: $project->title }}" loading="lazy">@else<div class="image-placeholder"></div>@endif</div>
                            <div class="admin-main-project-content">
                                <div class="admin-main-project-heading"><div><span class="eyebrow">{{ $project->category?->name ?: 'Architecture' }} @if($project->year)<i></i>{{ $project->year }}@endif</span><h3>{{ $project->title }}</h3><p>{{ $project->location ?: 'Location not set' }}</p></div><span class="project-count-pill">{{ $project->children->count() }} {{ $project->children->count() === 1 ? 'subproject' : 'subprojects' }}</span></div>
                                <div class="admin-main-project-actions"><button type="button" wire:click="edit({{ $project->id }})">Edit main project</button><button class="add-subproject-button" type="button" wire:click="createSubproject({{ $project->id }})">＋ Add subproject</button><a class="project-action-preview" href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener">↗ Preview website</a><button class="project-action-archive" type="button" wire:click="archive({{ $project->id }})" wire:confirm="Archive this main project? It will be hidden from the public site and can be restored.">Archive project</button></div>
                                @if($project->children->isNotEmpty())
                                    <details class="admin-subproject-disclosure"><summary><span>Show subprojects</span><b>{{ $project->children->count() }}</b><small>Click to view</small></summary>
                                        <div class="admin-subproject-list">
                                            @foreach($project->children as $child)
                                                @php $childCover = $child->coverImage ?: $child->images->first(); @endphp
                                                <article class="admin-subproject-row" wire:key="subproject-{{ $child->id }}">
                                                    <div class="admin-subproject-thumb">@if($childCover)<img src="{{ $childCover->publicUrl() }}" alt="" loading="lazy">@else<div class="image-placeholder"></div>@endif</div>
                                                    <div class="admin-subproject-info"><span>Subproject · {{ $project->category?->name ?: 'Inherited category' }}</span><strong>{{ $child->title }}</strong><small>{{ $child->location ?: 'Location not set' }} @if($child->year) · {{ $child->year }}@endif · {{ $child->images->count() }} images</small></div>
                                                    <div class="admin-subproject-actions"><button type="button" wire:click="edit({{ $child->id }})">Edit subproject</button><a href="{{ route('projects.show', $child->slug) }}" target="_blank" rel="noopener">Preview</a><button class="project-action-archive" type="button" wire:click="archive({{ $child->id }})" wire:confirm="Archive this subproject?">Archive project</button></div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </details>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="project-library-empty"><span aria-hidden="true">▱</span><h3>No main projects found</h3><p>Use the form to add a main project, then add its subprojects here.</p><button class="button" type="button" wire:click="resetForm">＋ Add your first project</button></div>
                    @endforelse
                </div>
            @endif
            {{ $projects->links() }}
        </section>
    </div>
</section>