<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $slug;

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function render()
    {
        $content = app(PublicContent::class);
        $settings = $content->settings();
        $project = $content->project($this->slug);

        abort_if($project === null, 404);

        $children = $content->projectChildrenPage($project->id, 12, $this->getPage('subprojects_page'));
        $images = $content->projectImagesPage($project->id, 12, $this->getPage('gallery_page'));

        return view('livewire.site.project-page', [
            'settings' => $settings,
            'project' => $project,
            'children' => $children,
            'images' => $images,
        ])->layout('layouts.public', [
            'settings' => $settings,
            'pageTitle' => $project->title.' · '.$settings->site_name,
            'metaDescription' => $project->description,
        ]);
    }

}
