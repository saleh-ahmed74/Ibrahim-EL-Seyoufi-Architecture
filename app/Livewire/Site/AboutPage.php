<?php

namespace App\Livewire\Site;

use App\Support\PublicContent;
use Livewire\Component;

class AboutPage extends Component
{
    public function render()
    {
        $content = app(PublicContent::class);
        $settings = $content->settings();

        return view('livewire.site.about-page', [
            'settings' => $settings,
        ])->layout('layouts.public', [
            'settings' => $settings,
            'pageTitle' => 'About · '.$settings->site_name,
            'metaDescription' => $settings->short_bio ?: $settings->meta_description,
        ]);
    }
}
