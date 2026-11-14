<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use Livewire\Attributes\Url;
use Livewire\Component;

class ProjectsPage extends Component
{
    #[Url(as: 'category')]
    public string $category = '';

    public function render()
    {
        $content = app(PublicContent::class);
        $settings = $content->settings();

        return view('livewire.site.projects-page', [
            'settings' => $settings,
            'categories' => $content->categories(),
            'projects' => $content->projects($this->category ?: null, 0),
        ])->layout('layouts.public', [
            'settings' => $settings,
            'pageTitle' => 'Projects · '.$settings->site_name,
            'metaDescription' => $settings->meta_description,
        ]);
    }

    public function filterByCategory(string $slug): void
    {
        $this->category = $slug;
    }
}
