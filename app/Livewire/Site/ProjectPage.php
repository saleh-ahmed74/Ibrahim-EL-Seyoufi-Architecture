<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use Livewire\Component;

class ProjectPage extends Component
{
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

        return view('livewire.site.project-page', [
            'settings' => $settings,
            'project' => $project,
        ])->layout('layouts.public', [
            'settings' => $settings,
            'pageTitle' => $project->title.' · '.$settings->site_name,
            'metaDescription' => $project->description,
        ]);
    }
}
