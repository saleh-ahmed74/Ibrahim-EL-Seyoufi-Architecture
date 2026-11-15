<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $attributes = ['is_featured' => false];

    protected $fillable = [
        'category_id', 'parent_id', 'title', 'slug', 'description',
        'location', 'year', 'sort_order', 'is_featured',
    ];

    protected function casts(): array
    {
        return ['year' => 'integer', 'sort_order' => 'integer', 'is_featured' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function coverImage(): HasOne
    {
        return $this->hasOne(ProjectImage::class)->where('is_cover', true);
    }

    public function scopeRootProjects(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
