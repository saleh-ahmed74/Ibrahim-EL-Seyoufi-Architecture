<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    protected $guarded = [];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }
    public function coverImage()
    {
        return $this->hasOne(ProjectImage::class)->where('is_cover', true);
    }
}
