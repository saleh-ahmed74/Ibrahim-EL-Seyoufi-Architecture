<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Inquiry;
use App\Models\PageView;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class Dashboard extends AdminComponent
{
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

        $files = Storage::disk('public')->allFiles();

        return view('livewire.admin.dashboard', [
            'totalProjects' => Project::query()->rootProjects()->count(),
            'totalSubprojects' => Project::query()->whereNotNull('parent_id')->count(),
            'totalCategories' => Category::query()->count(),
            'inquiriesCount' => Inquiry::query()->count(),
            'pageViewsCount' => PageView::query()->count(),
            'storageFiles' => count($files),
            'storageBytes' => collect($files)->sum(fn (string $file): int => Storage::disk('public')->size($file)),
            'projectsByCategory' => $projectsByCategory,
            'projectsByYear' => $projectsByYear,
        ]);
    }
}
