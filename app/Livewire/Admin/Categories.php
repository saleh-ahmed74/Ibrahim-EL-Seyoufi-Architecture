<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Support\PublicContent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Categories extends AdminComponent
{
    use WithPagination;

    public string $search = '';

    public ?int $editingId = null;

    public string $name = '';

    public string $slug = '';

    public string $description = '';

    public int $sortOrder = 0;

    public bool $isActive = true;

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
        ]);

        Category::query()->updateOrCreate(
            ['id' => $this->editingId],
            [
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'description' => $validated['description'] ?: null,
                'sort_order' => $validated['sortOrder'],
                'is_active' => $validated['isActive'],
            ],
        );

        $content->invalidate();
        $this->resetForm();
        session()->flash('status', 'Category saved.');
    }

    public function delete(int $id, PublicContent $content): void
    {
        Category::query()->findOrFail($id)->delete();
        $content->invalidate();
        session()->flash('status', 'Category deleted. Projects remain available without a category.');
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'name', 'slug', 'description', 'sortOrder', 'isActive']);
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
