<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Category, Project, SiteSetting};
use Illuminate\Http\Request;
class PublicController extends Controller
{
    public function projects(Request $r)
    {
        $q = Project::with(['category', 'images', 'coverImage']);
        if ($r->category)
            $q->whereHas('category', fn($x) => $x->where('slug', $r->category));
        return [
            'success' => true,
            'data' => $q->latest()->get()->map(fn($p) => $this->p($p))
        ];
    }
    public function project($slug)
    {
        return [    
            'success' => true,
            'data' => $this->p(Project::with(['category', 'images', 'coverImage'])
                ->where('slug', $slug)->firstOrFail())
        ];
    }
    private function i($x)
    {
        return $x ? [
            'url' => $x->image_path,
            'alt_text' => $x->alt_text,
            'sort_order' => $x->sort_order
        ] : null;
    }
    private function p($p)
    {
        return [
            'id' => $p->id,
            'title' => $p->title,
            'slug' => $p->slug,
            'description' => $p->description,
            'location' => $p->location,
            'year' => $p->year,
            'category' => $p->category?->only('name', 'slug'),
            'cover_image' => $this->i($p->coverImage ?: $p->images->first()),
            'images' => $p->images->map(fn($x) => $this->i($x))
        ];
    }
    public function categories()
    {
        return [
            'success' => true,
            'data' =>
                Category::where('is_active', true)->orderBy('sort_order')->get(['name', 'slug'])
        ];
    }
    public function settings()
    {
        return [
            'success' => true,
            'data' => SiteSetting::firstOrFail()
        ];
    }
}
