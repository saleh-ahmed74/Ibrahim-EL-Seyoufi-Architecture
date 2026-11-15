<?php

namespace App\Livewire\Admin;

use App\Models\SiteSetting as SiteSettingModel;
use App\Support\ImageCompressor;
use App\Support\HomepageCopy;
use App\Support\PublicContent;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
            'pinterest_url', 'meta_title', 'meta_description', 'homepage_copy',
        ]) ?? [
            'site_name' => '',
            'architect_name' => '',
            'professional_title' => '',
        ];
        $this->form['homepage_copy'] = HomepageCopy::merge($this->form['homepage_copy'] ?? null);
    }

    public function save(PublicContent $content): void
    {
        $rules = [
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
        ];

        foreach (HomepageCopy::labels() as $key => $label) {
            $rules["form.homepage_copy.{$key}.en"] = ['required', 'string', 'max:1000'];
            $rules["form.homepage_copy.{$key}.ar"] = ['required', 'string', 'max:1000'];
        }

        $validated = $this->validate($rules);

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
            report($exception);

            $this->addError('database', 'The settings could not be saved. Check the database and try again.');
            $this->notifyAdmin('Could not save site settings. Check the database and try again.', 'error');

            return;
        }

        Storage::disk('public')->delete($oldPaths);

        $content->invalidate();
        $this->reset(['logoImage', 'profileImage', 'heroImage']);
        $this->mount();
        $this->notifyAdmin('Site settings saved.');
    }

    public function removeCurrentImage(string $type, PublicContent $content): void
    {
        $images = [
            'logo' => ['logo_path', 'logoImage', 'Logo'],
            'profile' => ['profile_image', 'profileImage', 'Profile photo'],
            'hero' => ['hero_image', 'heroImage', 'Home image'],
        ];

        abort_unless(isset($images[$type]), 404);

        [$column, $uploadProperty, $label] = $images[$type];
        $settings = SiteSettingModel::query()->firstOrFail();
        $oldPath = $settings->{$column};

        if (! $oldPath) {
            return;
        }

        try {
            $settings->{$column} = null;
            $settings->save();
        } catch (\Throwable $exception) {
            report($exception);
            $this->addError('database', "Could not remove the {$label}. Check the database and try again.");
            $this->notifyAdmin("Could not remove the {$label}.", 'error');

            return;
        }

        if (! filter_var($oldPath, FILTER_VALIDATE_URL)) {
            $storagePath = ltrim($oldPath, '/');
            $storagePath = Str::startsWith($storagePath, 'storage/') ? substr($storagePath, 8) : $storagePath;
            $storagePath = Str::startsWith($storagePath, 'public/') ? substr($storagePath, 7) : $storagePath;

            if ($storagePath !== '' && ! Str::contains($storagePath, '..')) {
                Storage::disk('public')->delete($storagePath);
            }
        }

        $content->invalidate();
        $this->reset($uploadProperty);
        $this->notifyAdmin("{$label} removed.");
    }

    public function render()
    {
        return view('livewire.admin.site-settings', [
            'settings' => SiteSettingModel::query()->first(),
            'homepageCopyFields' => HomepageCopy::labels(),
        ]);
    }
}
