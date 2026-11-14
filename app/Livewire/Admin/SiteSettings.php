<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting as SiteSettingModel;
use App\Support\ImageCompressor;
use App\Support\PublicContent;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin')]
class SiteSettings extends AdminComponent
{
    use WithFileUploads;

    public array $form = [];

    public $logoImage;

    public $profileImage;

    public $heroImage;

    public function mount(): void
    {
        $settings = SiteSettingModel::query()->first();

        $this->form = $settings?->only([
            'site_name', 'architect_name', 'professional_title', 'short_bio', 'bio',
            'education', 'experience', 'design_philosophy', 'specializations',
            'phone', 'email', 'whatsapp_number', 'whatsapp_message', 'address',
            'instagram_url', 'facebook_url', 'linkedin_url', 'youtube_url',
            'pinterest_url', 'meta_title', 'meta_description',
        ]) ?? [
            'site_name' => '',
            'architect_name' => '',
            'professional_title' => '',
        ];
    }

    public function save(PublicContent $content): void
    {
        $validated = $this->validate([
            'form.site_name' => ['required', 'string', 'max:255'],
            'form.architect_name' => ['required', 'string', 'max:255'],
            'form.professional_title' => ['required', 'string', 'max:255'],
            'form.short_bio' => ['nullable', 'string', 'max:5000'],
            'form.bio' => ['nullable', 'string', 'max:20000'],
            'form.education' => ['nullable', 'string', 'max:5000'],
            'form.experience' => ['nullable', 'string', 'max:5000'],
            'form.design_philosophy' => ['nullable', 'string', 'max:5000'],
            'form.specializations' => ['nullable', 'string', 'max:5000'],
            'form.phone' => ['nullable', 'string', 'max:60'],
            'form.email' => ['nullable', 'email', 'max:255'],
            'form.whatsapp_number' => ['nullable', 'string', 'max:60'],
            'form.whatsapp_message' => ['nullable', 'string', 'max:1000'],
            'form.address' => ['nullable', 'string', 'max:255'],
            'form.instagram_url' => ['nullable', 'url', 'max:255'],
            'form.facebook_url' => ['nullable', 'url', 'max:255'],
            'form.linkedin_url' => ['nullable', 'url', 'max:255'],
            'form.youtube_url' => ['nullable', 'url', 'max:255'],
            'form.pinterest_url' => ['nullable', 'url', 'max:255'],
            'form.meta_title' => ['nullable', 'string', 'max:255'],
            'form.meta_description' => ['nullable', 'string', 'max:5000'],
            'logoImage' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'profileImage' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192'],
            'heroImage' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:12288'],
        ]);

        $settings = SiteSettingModel::query()->first() ?? new SiteSettingModel();
        $settings->fill($validated['form']);

        $oldPaths = [];
        $newPaths = [];

        try {
            foreach ([
                'logoImage' => ['logo_path', 'branding', 800],
                'profileImage' => ['profile_image', 'profile', 1000],
                'heroImage' => ['hero_image', 'hero', 2200],
            ] as $property => [$column, $directory, $maxWidth]) {
                $upload = $this->{$property};

                if (! $upload) {
                    continue;
                }

                $oldPath = $settings->{$column};
                $settings->{$column} = ImageCompressor::store($upload, 'public', $directory, $maxWidth, 78);
                $newPaths[] = $settings->{$column};

                if ($oldPath) {
                    $oldPaths[] = $oldPath;
                }
            }

            $settings->save();
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($newPaths);

            throw $exception;
        }

        Storage::disk('public')->delete($oldPaths);

        $content->invalidate();
        $this->reset(['logoImage', 'profileImage', 'heroImage']);
        $this->mount();
        session()->flash('status', 'Site settings saved.');
    }

    public function render()
    {
        return view('livewire.admin.site-settings', [
            'settings' => SiteSettingModel::query()->first(),
        ]);
    }
}
