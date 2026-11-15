<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Support\ImageCompressor;
use App\Support\PublicContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Projects extends AdminComponent
{
    use WithFileUploads, WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $search = '';

    public string $status = 'active';

    public ?int $editingId = null;

    public ?int $categoryId = null;

    public ?int $parentId = null;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $location = '';

    public ?int $year = null;

    public bool $isFeatured = false;

    public int $sortOrder = 0;

    public array $imageUploads = [];

    public ?int $coverImageId = null;

    public ?int $coverUploadIndex = null;

    public bool $askAddAnotherSubproject = false;

    public string $lastSavedSubprojectParent = '';

    public function createSubproject(int $parentId): void
    {
        $parent = Project::query()->rootProjects()->findOrFail($parentId);
        $this->resetForm();
        $this->parentId = $parent->id;
        $this->sortOrder = ((int) $parent->children()->max('sort_order')) + 1;
        $this->dispatch('focus-project-editor');
    }
    public function updatedParentId(): void
    {
        $this->categoryId = null;
        if ($this->parentId !== null) {
            $this->isFeatured = false;
        }
        $this->resetValidation('categoryId');
    }
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $project = Project::query()->withTrashed()->findOrFail($id);

        $this->editingId = $project->id;
        $this->categoryId = $project->category_id;
        $this->parentId = $project->parent_id;
        $this->title = $project->title;
        $this->slug = $project->slug;
        $this->description = $project->description;
        $this->location = $project->location ?? '';
        $this->year = $project->year;
        $this->isFeatured = (bool) $project->is_featured;
        $this->sortOrder = $project->sort_order;
        $this->coverImageId = $project->images()->where('is_cover', true)->value('id');
        $this->status = $project->trashed() ? 'archived' : 'active';
        $this->resetPage('editor-images-page');
        $this->dispatch('focus-project-editor');
    }

    public function save(PublicContent $content): void
    {
        $this->slug = $this->slug ?: Str::slug($this->title);

        $validated = $this->validate([
            'categoryId' => [$this->parentId ? 'nullable' : 'required', 'integer', Rule::exists('categories', 'id')],
            'parentId' => [
                'nullable',
                'integer',
                Rule::exists('projects', 'id')->whereNull('parent_id')->whereNull('deleted_at'),
                Rule::notIn(array_filter([$this->editingId])),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('projects', 'slug')->ignore($this->editingId)],
            'description' => ['required', 'string', 'max:20000'],
            'location' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1800', 'max:2200'],
            'isFeatured' => ['required', 'boolean'],
            'sortOrder' => ['required', 'integer', 'min:0', 'max:65535'],
            'imageUploads' => $this->editingId ? ['array', 'max:20'] : ['required', 'array', 'min:1', 'max:20'],
            'imageUploads.*' => ['image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'coverUploadIndex' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($this->editingId && $this->parentId && Project::query()->withTrashed()->where('parent_id', $this->editingId)->exists()) {
            $this->addError('parentId', 'A project that already contains subprojects must remain a main project.');

            return;
        }

        $resolvedParent = $validated['parentId'] !== null
            ? Project::query()->rootProjects()->findOrFail($validated['parentId'])
            : null;
        $resolvedCategoryId = $validated['parentId'] !== null
            ? $resolvedParent->category_id
            : ($validated['categoryId'] ?: null);
        $wasNewSubproject = $this->editingId === null && $validated['parentId'] !== null;
        $savedParentId = $validated['parentId'];
        $savedParentTitle = $resolvedParent?->title ?? '';

        $storedPaths = [];

        try {
            DB::transaction(function () use ($validated, $resolvedCategoryId, &$storedPaths): void {
            $project = Project::query()->withTrashed()->updateOrCreate(
                ['id' => $this->editingId],
                [
                    'category_id' => $resolvedCategoryId,
                    'parent_id' => $validated['parentId'] ?: null,
                    'title' => $validated['title'],
                    'slug' => $validated['slug'],
                    'description' => $validated['description'],
                    'location' => $validated['location'] ?: null,
                    'year' => $validated['year'] ?: null,
                    'sort_order' => $validated['sortOrder'],
                    'is_featured' => $validated['parentId'] === null && $validated['isFeatured'],
                ],
            );

            if ($project->trashed()) {
                $project->restore();
            }

            if ($project->parent_id === null) {
                Project::query()->where('parent_id', $project->id)->update(['category_id' => $resolvedCategoryId]);
            }

            $sortOrder = $project->images()->count();

            foreach ($this->imageUploads as $index => $imageUpload) {
                $path = ImageCompressor::store($imageUpload, 'public', 'projects', 1920, 75);
                $storedPaths[] = $path;
                $isCover = $this->coverUploadIndex === $index
                    || ($this->coverImageId === null && $index === 0);

                if ($isCover) {
                    $project->images()->update(['is_cover' => false]);
                }

                $project->images()->create([
                    'image_path' => $path,
                    'alt_text' => $project->title,
                    'sort_order' => $sortOrder++,
                    'is_cover' => $isCover,
                ]);

                if ($isCover) {
                    $this->coverImageId = null;
                }
            }

            if ($project->images()->where('is_cover', true)->doesntExist()) {
                $firstImage = $project->images()->oldest('id')->first();

                if ($firstImage) {
                    $firstImage->update(['is_cover' => true]);
                }
            }

            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);
            report($exception);
            $this->addError('database', 'The project could not be saved. Check the database and try again.');
            $this->notifyAdmin('Could not save the project. Check the database and try again.', 'error');

            return;
        }

        $content->invalidate();
        $this->resetForm();

        if ($wasNewSubproject && $savedParentId !== null) {
            $this->parentId = $savedParentId;
            $this->sortOrder = ((int) Project::query()->where('parent_id', $savedParentId)->max('sort_order')) + 1;
            $this->lastSavedSubprojectParent = $savedParentTitle;
            $this->askAddAnotherSubproject = true;
            $this->notifyAdmin('Subproject saved.');

            return;
        }

        $this->notifyAdmin('Project saved.');
    }

    public function setCover(int $imageId, PublicContent $content): void
    {
        $project = Project::query()->withTrashed()->findOrFail($this->editingId);
        abort_unless($project->images()->whereKey($imageId)->exists(), 404);

        DB::transaction(function () use ($project, $imageId): void {
            $project->images()->update(['is_cover' => false]);
            $project->images()->whereKey($imageId)->update(['is_cover' => true]);
        });

        $this->coverImageId = $imageId;
        $content->invalidate();
        $this->resetPage('editor-images-page');
        $this->notifyAdmin('Project cover image updated.');
    }

    public function removeImage(int $imageId, PublicContent $content): void
    {
        $project = Project::query()->withTrashed()->findOrFail($this->editingId);
        $image = $project->images()->whereKey($imageId)->firstOrFail();

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        if ($this->coverImageId === $imageId) {
            $this->coverImageId = null;
            $next = $project->images()->oldest('id')->first();

            if ($next) {
                $next->update(['is_cover' => true]);
                $this->coverImageId = $next->id;
            }
        }

        $this->resetPage('editor-images-page');
        $content->invalidate();
        $this->notifyAdmin('Project image removed.');
    }

    public function archive(int $id, PublicContent $content): void
    {
        Project::query()->findOrFail($id)->delete();
        $content->invalidate();
        $this->notifyAdmin('Project archived and hidden from the public website.');
    }

    public function restore(int $id, PublicContent $content): void
    {
        Project::query()->onlyTrashed()->findOrFail($id)->restore();
        $content->invalidate();
        $this->notifyAdmin('Project restored.');
    }

    public function forceDelete(int $id, PublicContent $content): void
    {
        $project = Project::query()->onlyTrashed()->with('images')->findOrFail($id);

        DB::transaction(function () use ($project): void {
            $children = Project::query()->withTrashed()->where('parent_id', $project->id)->with('images')->get();

            foreach ($children as $child) {
                foreach ($child->images as $image) {
                    Storage::disk('public')->delete($image->image_path);
                }

                $child->forceDelete();
            }

            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $project->forceDelete();
        });

        $content->invalidate();
        $this->notifyAdmin('Archived project and its subprojects permanently deleted.');
    }

    public function continueAddingSubproject(): void
    {
        $this->askAddAnotherSubproject = false;
        $this->resetValidation();
    }

    public function finishAddingSubprojects(): void
    {
        $this->resetForm();
    }
    public function resetForm(): void
    {
        $this->reset([
            'editingId', 'categoryId', 'parentId', 'title', 'slug', 'description',
            'location', 'year', 'isFeatured', 'imageUploads', 'coverImageId', 'coverUploadIndex',
            'askAddAnotherSubproject', 'lastSavedSubprojectParent',
        ]);
        $this->sortOrder = 0;
        $this->resetValidation();
        $this->resetPage('editor-images-page');
        $this->dispatch('focus-project-editor');
    }

    public function render()
    {
        $projects = Project::query()
            ->when($this->status === 'archived', fn ($query) => $query->onlyTrashed())
            ->when($this->status !== 'archived', fn ($query) => $query->rootProjects())
            ->with($this->status === 'archived'
                ? ['parent', 'category', 'coverImage', 'images' => fn ($images) => $images->limit(1)]
                : [
                    'category',
                    'coverImage',
                    'images' => fn ($images) => $images->limit(1),
                ])
            ->withCount('children')
            ->when($this->search, fn ($query) => $query->where(fn ($query) => $query
                ->where('title', 'like', '%'.$this->search.'%')
                ->orWhere('slug', 'like', '%'.$this->search.'%')
                ->orWhere('location', 'like', '%'.$this->search.'%')
                ->when($this->status !== 'archived', fn ($query) => $query->orWhereHas('children', fn ($children) => $children
                    ->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('slug', 'like', '%'.$this->search.'%')
                    ->orWhere('location', 'like', '%'.$this->search.'%')))))
            ->when($this->status !== 'archived', fn ($query) => $query->orderBy('sort_order')->orderByDesc('year'))
            ->orderBy('id')
            ->paginate($this->status === 'archived' ? 12 : 9);

        if ($this->status !== 'archived') {
            $projects->getCollection()->each(function (Project $project): void {
                $project->setRelation('paginatedChildren', $project->children()
                    ->with(['category', 'coverImage', 'images' => fn ($images) => $images->limit(1)])
                    ->withCount('images')
                    ->paginate(8, ['*'], 'subprojects-'.$project->id));
            });
        }

        $editorImages = $this->editingId
            ? Project::query()->withTrashed()->find($this->editingId)?->images()->paginate(12, ['*'], 'editor-images-page')
            : null;

        return view('livewire.admin.projects', [
            'projects' => $projects,
            'editorImages' => $editorImages,
            'categories' => Category::query()->orderBy('sort_order')->get(),
            'selectedParent' => $this->parentId ? Project::query()->with('category')->find($this->parentId) : null,
            'parentProjects' => Project::query()
                ->rootProjects()
                ->when($this->editingId, fn ($query) => $query->where('id', '<>', $this->editingId))
                ->orderBy('title')
                ->get(),
        ]);
    }
}
