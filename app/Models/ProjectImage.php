<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProjectImage extends Model
{
    protected $fillable = ['project_id', 'image_path', 'alt_text', 'sort_order', 'is_cover'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_cover' => 'boolean'];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function publicUrl(): string
    {
        $path = $this->image_path;

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
