<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Support\ImageCompressor;
use App\Support\PublicContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Categories extends AdminComponent
{
    use WithFileUploads, WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

    public $categoryImage;

    public ?string $currentImageUrl = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $category = Category::query()->findOrFail($id);

        $this->editingId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->sortOrder = $category->sort_order;
        $this->isActive = $category->is_active;
        $this->currentImageUrl = $category->image_path ? Storage::disk('public')->url($category->image_path) : null;
        $this->resetValidation();
    }

    public function save(PublicContent $content): void
    {
        $this->slug = $this->slug ?: Str::slug($this->name);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:5000'],
            'sortOrder' => ['required', 'integer', 'min:0', 'max:65535'],
            'isActive' => ['boolean'],
            'categoryImage' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
        ]);

        $storedPath = null;
        $oldPath = null;

        try {
            if ($this->categoryImage) {
                $storedPath = ImageCompressor::store($this->categoryImage, 'public', 'categories', 1200, 78);
            }

            $category = $this->editingId ? Category::query()->findOrFail($this->editingId) : new Category();
            $oldPath = $category->image_path;
            $category->fill([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?: null,
                'sort_order' => $validated['sortOrder'],
                'is_active' => $validated['isActive'],
            ]);

            if ($storedPath) {
                $category->image_path = $storedPath;
            }

            $category->save();
        } catch (\Throwable $exception) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $exception;
        }

        if ($storedPath && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $content->invalidate();
        $this->resetForm();
        session()->flash('status', 'Category saved.');
    }

    public function delete(int $id, PublicContent $content): void
    {
        $category = Category::query()->findOrFail($id);
        $imagePath = $category->image_path;
        $category->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $content->invalidate();
        session()->flash('status', 'Category deleted. Projects remain available without a category.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'slug', 'description', 'sortOrder', 'isActive', 'categoryImage', 'currentImageUrl']);
        $this->sortOrder = 0;
        $this->isActive = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'categories' => Category::query()
                ->withCount('projects')
                ->when($this->search, fn ($query) => $query->where(fn ($query) => $query->where('name', 'like', '%'.$this->search.'%')->orWhere('slug', 'like', '%'.$this->search.'%')))
                ->orderBy('sort_order')
                ->paginate(12),
        ]);
    }
}