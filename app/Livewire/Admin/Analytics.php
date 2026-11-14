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
class Analytics extends AdminComponent
{
    public function render()
    {
        $files = Storage::disk('public')->allFiles();

        return view('livewire.admin.analytics', [
            'totalProjects' => Project::query()->whereNull('parent_id')->count(),
            'totalSubprojects' => Project::query()->whereNotNull('parent_id')->count(),
            'totalCategories' => Category::query()->count(),
            'inquiriesCount' => Inquiry::query()->count(),
            'pageViewsCount' => PageView::query()->count(),
            'storageFiles' => count($files),
            'storageBytes' => collect($files)
                ->sum(fn (string $file): int => Storage::disk('public')->size($file)),
        ]);
    }
}
