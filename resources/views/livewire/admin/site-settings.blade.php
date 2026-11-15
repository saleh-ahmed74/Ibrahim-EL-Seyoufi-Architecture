<section class="admin-page">
    <div class="admin-page-heading"><div><span class="eyebrow">Public website</span><h1>Site settings</h1><p>Manage the site identity, home image, and public contact links.</p></div></div>
    <form class="admin-panel settings-form" wire:submit="save" enctype="multipart/form-data">
        @error('database')<div class="field-error" role="alert">{{ $message }}</div>@enderror
        <section class="settings-section"><h2>Brand and home page</h2>
            <div class="form-grid">
                <div><label for="site-name">Site name</label><input id="site-name" wire:model="form.site_name" required>@error('form.site_name')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div><label for="architect-name">Architect or studio name</label><input id="architect-name" wire:model="form.architect_name" required>@error('form.architect_name')<span class="field-error">{{ $message }}</span>@enderror</div>
                <div><label for="professional-title">Professional title</label><input id="professional-title" wire:model="form.professional_title" required>@error('form.professional_title')<span class="field-error">{{ $message }}</span>@enderror</div>
            </div>
            <label for="logo-image">Logo image (JPG, PNG or WebP)</label>@if($settings?->logo_path)<div class="settings-current-image"><img class="settings-image-preview logo-preview" src="{{ $settings->publicImageUrl($settings->logo_path) }}" alt="Current site logo"><button class="danger-link" type="button" wire:click="removeCurrentImage('logo')" wire:confirm="Remove the current logo from the site?">Remove current logo</button></div>@endif<input id="logo-image" type="file" wire:model="logoImage" accept="image/jpeg,image/png,image/webp">@error('logoImage')<span class="field-error">{{ $message }}</span>@enderror
            @if($logoImage && $logoImage->isPreviewable())<img class="settings-image-preview logo-preview" src="{{ $logoImage->temporaryUrl() }}" alt="New logo preview">@endif
            <label for="hero-image">Home image (shown across two thirds of the mobile screen)</label>@if($settings?->hero_image)<div class="settings-current-image"><img class="settings-image-preview hero-preview" src="{{ $settings->publicImageUrl($settings->hero_image) }}" alt="Current home image"><button class="danger-link" type="button" wire:click="removeCurrentImage('hero')" wire:confirm="Remove the current home image from the site?">Remove current home image</button></div>@endif<input id="hero-image" type="file" wire:model="heroImage" accept="image/jpeg,image/png,image/webp">@error('heroImage')<span class="field-error">{{ $message }}</span>@enderror
            @if($heroImage && $heroImage->isPreviewable())<img class="settings-image-preview hero-preview" src="{{ $heroImage->temporaryUrl() }}" alt="New home image preview">@endif
        </section>

        <section class="settings-section homepage-copy-settings">
            <h2>Homepage wording</h2>
            <p>Edit the English and Arabic copy shown on the homepage, including its navigation and footer. Leave the wording as you want it displayed.</p>
            <div class="homepage-copy-list">
                @foreach($homepageCopyFields as $key => $label)
                    <fieldset class="homepage-copy-field">
                        <legend>{{ $label }}</legend>
                        <div class="form-grid">
                            <div><label for="homepage-copy-{{ $key }}-en">English</label><textarea id="homepage-copy-{{ $key }}-en" dir="ltr" wire:model="form.homepage_copy.{{ $key }}.en" rows="2"></textarea>@error('form.homepage_copy.'.$key.'.en')<span class="field-error">{{ $message }}</span>@enderror</div>
                            <div><label for="homepage-copy-{{ $key }}-ar">Arabic</label><textarea id="homepage-copy-{{ $key }}-ar" dir="rtl" wire:model="form.homepage_copy.{{ $key }}.ar" rows="2"></textarea>@error('form.homepage_copy.'.$key.'.ar')<span class="field-error">{{ $message }}</span>@enderror</div>
                        </div>
                    </fieldset>
                @endforeach
            </div>
        </section>

        <section class="settings-section"><h2>About</h2>
            @foreach(['short_bio' => 'Short introduction', 'bio' => 'Biography', 'education' => 'Education', 'experience' => 'Experience', 'design_philosophy' => 'Design philosophy', 'specializations' => 'Specializations'] as $field => $label)
                <label for="setting-{{ $field }}">{{ $label }}</label><textarea id="setting-{{ $field }}" wire:model="form.{{ $field }}" rows="{{ $field === 'bio' ? 5 : 3 }}"></textarea>@error('form.'.$field)<span class="field-error">{{ $message }}</span>@enderror
            @endforeach
            <label for="profile-image">Profile photo</label>@if($settings?->profile_image)<div class="settings-current-image"><img class="settings-image-preview profile-preview" src="{{ $settings->publicImageUrl($settings->profile_image) }}" alt="Current profile photo"><button class="danger-link" type="button" wire:click="removeCurrentImage('profile')" wire:confirm="Remove the current profile photo from the site?">Remove current photo</button></div>@endif<input id="profile-image" type="file" wire:model="profileImage" accept="image/jpeg,image/png,image/webp">@error('profileImage')<span class="field-error">{{ $message }}</span>@enderror
            @if($profileImage && $profileImage->isPreviewable())<img class="settings-image-preview profile-preview" src="{{ $profileImage->temporaryUrl() }}" alt="New profile photo preview">@endif
        </section>

        <section class="settings-section"><h2>Contact and social links</h2><div class="form-grid">
            @foreach(['phone' => 'Phone', 'email' => 'Email', 'whatsapp_number' => 'WhatsApp number', 'address' => 'Address', 'instagram_url' => 'Instagram URL', 'facebook_url' => 'Facebook URL', 'linkedin_url' => 'LinkedIn URL', 'youtube_url' => 'YouTube URL', 'pinterest_url' => 'Pinterest URL'] as $field => $label)
                <div><label for="setting-{{ $field }}">{{ $label }}</label><input id="setting-{{ $field }}" wire:model="form.{{ $field }}" @if(str_ends_with($field, '_url')) type="url" @endif>@error('form.'.$field)<span class="field-error">{{ $message }}</span>@enderror</div>
            @endforeach
        </div><label for="whatsapp-message">WhatsApp message</label><textarea id="whatsapp-message" wire:model="form.whatsapp_message" rows="2"></textarea></section>

        <section class="settings-section"><h2>Search preview</h2>
            <label for="meta-title">Page title</label><input id="meta-title" wire:model="form.meta_title">@error('form.meta_title')<span class="field-error">{{ $message }}</span>@enderror
            <label for="meta-description">Page description</label><textarea id="meta-description" wire:model="form.meta_description" rows="3"></textarea>@error('form.meta_description')<span class="field-error">{{ $message }}</span>@enderror
        </section>
        <div class="form-actions"><button class="button" type="submit" wire:loading.attr="disabled">Save settings</button><span wire:loading class="loading-copy">Saving…</span></div>
    </form>
</section>
