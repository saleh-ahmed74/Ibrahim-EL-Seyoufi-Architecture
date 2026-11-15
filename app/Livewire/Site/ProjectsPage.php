<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsPage extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url(as: 'category')]
    public string $category = '';

    public function render()
    {
        $content = app(PublicContent::class);
        $settings = $content->settings();

        return view('livewire.site.projects-page', [
            'settings' => $settings,
            'projects' => $content->projectsPage($this->category ?: null, 24, $this->getPage()),
        ])->layout('layouts.public', [
            'settings' => $settings,
            'pageTitle' => 'Projects · '.$settings->site_name,
            'metaDescription' => $settings->meta_description,
        ]);
    }

}
