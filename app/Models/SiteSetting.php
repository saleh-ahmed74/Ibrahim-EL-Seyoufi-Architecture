<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'architect_name', 'professional_title', 'short_bio', 'bio',
        'education', 'experience', 'design_philosophy', 'specializations',
        'phone', 'email', 'whatsapp_number', 'whatsapp_message', 'address',
        'instagram_url', 'facebook_url', 'linkedin_url', 'youtube_url',
        'pinterest_url', 'profile_image', 'hero_image', 'logo_path',
        'meta_title', 'meta_description',
    ];

    public function publicImageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, '/storage/') || str_starts_with($path, 'storage/')) {
            return '/'.ltrim($path, '/');
        }

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        return Storage::disk('public')->url($path);
    }
}
