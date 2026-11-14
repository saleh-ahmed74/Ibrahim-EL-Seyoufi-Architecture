<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'image_path', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer', 'is_active' => 'boolean'];
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function displayImageUrl(): ?string
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        $projects = $this->relationLoaded('projects')
            ? $this->projects
            : $this->projects()->rootProjects()->with(['coverImage', 'images'])->get();

        foreach ($projects as $project) {
            $image = $project->coverImage ?: $project->images->first();

            if ($image) {
                return $image->publicUrl();
            }
        }

        return null;
    }
}