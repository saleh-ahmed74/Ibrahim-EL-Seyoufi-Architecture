<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use Livewire\Attributes\Url;
use Livewire\Component;

class HomePage extends Component
{
    #[Url(as: 'category')]
    public string $category = '';

    public function render()
    {
        $content = app(PublicContent::class);
        $settings = $content->settings();
        $projects = $content->projects($this->category ?: null, 6);
        $heroSlides = collect();

        if ($settings->hero_image) {
            $heroSlides->push([
                'url' => $settings->publicImageUrl($settings->hero_image),
                'alt' => $settings->site_name,
            ]);
        }

        foreach ($projects as $project) {
            $cover = $project->coverImage ?: $project->images->first();

            if ($cover) {
                $heroSlides->push([
                    'url' => $cover->publicUrl(),
                    'alt' => $cover->alt_text ?: $project->title,
                ]);
            }
        }

        return view('livewire.site.home-page', [
            'settings' => $settings,
            'categories' => $content->categories(),
            'projects' => $projects,
            'heroSlides' => $heroSlides->unique('url')->values(),
        ])->layout('layouts.public', [
            'settings' => $settings,
            'pageTitle' => $settings->meta_title ?: $settings->site_name,
            'metaDescription' => $settings->meta_description ?: $settings->short_bio,
        ]);
    }

    public function filterByCategory(string $slug): void
    {
        $this->category = $slug;
    }
}
