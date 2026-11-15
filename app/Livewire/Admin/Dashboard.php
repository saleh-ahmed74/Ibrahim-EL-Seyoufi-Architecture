<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Inquiry;
use App\Models\PageView;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Dashboard extends AdminComponent
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $categoryFilter = '';

    public string $yearFilter = '';

    public string $projectSearch = '';

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedYearFilter(): void
    {
        $this->resetPage();
    }

    public function updatedProjectSearch(): void
    {
        $this->resetPage();
    }

    public function clearProjectFilters(): void
    {
        $this->reset(['categoryFilter', 'yearFilter', 'projectSearch']);
        $this->resetPage();
    }

    public function render()
    {
        $projectsByCategory = Category::query()
            ->withCount(['projects as projects_count' => fn ($query) => $query->whereNull('parent_id')])
            ->orderBy('sort_order')
            ->get(['id', 'name', 'sort_order'])
            ->map(fn (Category $category): array => ['name' => $category->name, 'count' => $category->projects_count])
            ->all();

        $projectsByYear = Project::query()
            ->rootProjects()
            ->whereNotNull('year')
            ->selectRaw('year, count(*) as count')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->map(fn (Project $project): array => ['year' => $project->year, 'count' => $project->count])
            ->all();

        $portfolioProjects = Project::query()
            ->rootProjects()
            ->withCount('children')
            ->with(['category', 'coverImage', 'images' => fn ($images) => $images->limit(1)])
            ->when($this->categoryFilter !== '', fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $this->categoryFilter)))
            ->when($this->yearFilter !== '', fn ($query) => $query->where('year', (int) $this->yearFilter))
            ->when(trim($this->projectSearch) !== '', fn ($query) => $query->where(function ($query): void {
                $search = '%'.trim($this->projectSearch).'%';
                $query->where('title', 'like', $search)
                    ->orWhere('slug', 'like', $search)
                    ->orWhere('location', 'like', $search);
            }))
            ->orderBy('sort_order')
            ->orderByDesc('year')
            ->paginate(8);

        $files = Storage::disk('public')->allFiles();

        return view('livewire.admin.dashboard', [
            'totalProjects' => Project::query()->rootProjects()->count(),
            'totalSubprojects' => Project::query()->whereNotNull('parent_id')->count(),
            'totalCategories' => Category::query()->count(),
            'inquiriesCount' => Inquiry::query()->count(),
            'pageViewsCount' => PageView::query()->count(),
            'storageFiles' => count($files),
            'storageBytes' => collect($files)->sum(fn (string $file): int => Storage::disk('public')->size($file)),
            'portfolioProjects' => $portfolioProjects,
            'projectFilterCategories' => Category::query()->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'slug']),
            'projectFilterYears' => Project::query()->rootProjects()->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year'),
            'projectsByCategory' => $projectsByCategory,
            'projectsByYear' => $projectsByYear,
        ]);
    }
}
