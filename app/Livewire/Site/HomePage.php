<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use App\Support\HomepageCopy;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        $content = app(PublicContent::class);
        $settings = $content->settings();
        $homepageCopy = HomepageCopy::merge($settings->homepage_copy);
        $projects = $content->projects(null, 6, featuredOnly: true);
        $heroSlides = collect();

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
            'homepageCopy' => $homepageCopy,
            'projects' => $projects,
            'heroSlides' => $heroSlides->unique('url')->values(),
            'heroPortrait' => $settings->hero_image ? $settings->publicImageUrl($settings->hero_image) : null,
        ])->layout('layouts.public', [
            'settings' => $settings,
            'homepageCopy' => $homepageCopy,
            'pageTitle' => $settings->meta_title ?: $settings->site_name,
            'metaDescription' => $settings->meta_description ?: $settings->short_bio,
        ]);
    }

}
