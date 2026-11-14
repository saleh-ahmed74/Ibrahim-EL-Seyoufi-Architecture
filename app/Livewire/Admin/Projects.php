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
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Projects extends AdminComponent
{
    use WithFileUploads, WithPagination;

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

    public int $sortOrder = 0;

    public array $imageUploads = [];

    public array $existingImages = [];

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
        $this->resetValidation('categoryId');
    }
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $project = Project::query()->withTrashed()->with(['images'])->findOrFail($id);

        $this->editingId = $project->id;
        $this->categoryId = $project->category_id;
        $this->parentId = $project->parent_id;
        $this->title = $project->title;
        $this->slug = $project->slug;
        $this->description = $project->description;
        $this->location = $project->location ?? '';
        $this->year = $project->year;
        $this->sortOrder = $project->sort_order;
        $this->existingImages = $project->images->map(fn (ProjectImage $image): array => [
            'id' => $image->id,
            'url' => $image->publicUrl(),
            'alt_text' => $image->alt_text,
            'sort_order' => $image->sort_order,
            'is_cover' => $image->is_cover,
        ])->all();
        $this->coverImageId = $project->images->firstWhere('is_cover', true)?->id;
        $this->status = $project->trashed() ? 'archived' : 'active';
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
            'sortOrder' => ['required', 'integer', 'min:0', 'max:65535'],
            'imageUploads' => $this->editingId || $this->existingImages ? ['array', 'max:20'] : ['required', 'array', 'min:1', 'max:20'],
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
                    || ($this->coverImageId === null && $this->existingImages === [] && $index === 0);

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

            throw $exception;
        }

        $content->invalidate();
        $this->resetForm();

        if ($wasNewSubproject && $savedParentId !== null) {
            $this->parentId = $savedParentId;
            $this->sortOrder = ((int) Project::query()->where('parent_id', $savedParentId)->max('sort_order')) + 1;
            $this->lastSavedSubprojectParent = $savedParentTitle;
            $this->askAddAnotherSubproject = true;
            session()->flash('status', 'Subproject saved.');

            return;
        }

        session()->flash('status', 'Project saved.');
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
        $this->existingImages = $project->images()->get()->map(fn (ProjectImage $image): array => [
            'id' => $image->id,
            'url' => $image->publicUrl(),
            'alt_text' => $image->alt_text,
            'sort_order' => $image->sort_order,
            'is_cover' => $image->is_cover,
        ])->all();
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

        $this->existingImages = $project->images()->get()->map(fn (ProjectImage $image): array => [
            'id' => $image->id,
            'url' => $image->publicUrl(),
            'alt_text' => $image->alt_text,
            'sort_order' => $image->sort_order,
            'is_cover' => $image->is_cover,
        ])->all();
        $content->invalidate();
    }

    public function archive(int $id, PublicContent $content): void
    {
        Project::query()->findOrFail($id)->delete();
        $content->invalidate();
        session()->flash('status', 'Project archived and hidden from the public website.');
    }

    public function restore(int $id, PublicContent $content): void
    {
        Project::query()->onlyTrashed()->findOrFail($id)->restore();
        $content->invalidate();
        session()->flash('status', 'Project restored.');
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
        session()->flash('status', 'Archived project and its subprojects permanently deleted.');
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
            'location', 'year', 'imageUploads', 'existingImages', 'coverImageId', 'coverUploadIndex',
            'askAddAnotherSubproject', 'lastSavedSubprojectParent',
        ]);
        $this->sortOrder = 0;
        $this->resetValidation();
        $this->dispatch('focus-project-editor');
    }

    public function render()
    {
        $projects = Project::query()
            ->when($this->status === 'archived', fn ($query) => $query->onlyTrashed())
            ->when($this->status !== 'archived', fn ($query) => $query->rootProjects())
            ->with($this->status === 'archived'
                ? ['parent', 'category', 'images', 'coverImage']
                : ['category', 'images', 'coverImage', 'children.category', 'children.images', 'children.coverImage'])
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

        return view('livewire.admin.projects', [
            'projects' => $projects,
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
