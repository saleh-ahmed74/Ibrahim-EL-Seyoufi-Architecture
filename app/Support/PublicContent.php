<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Throwable;

class PublicContent
{
    private const CACHE_TTL_MINUTES = 10;

    public function settings(): SiteSetting
    {
        return $this->remember(
            'settings',
            fn () => SiteSetting::query()->first(),
            new SiteSetting([
                'site_name' => 'Architecture Portfolio',
                'architect_name' => 'Architecture Studio',
                'professional_title' => 'Architectural design',
            ]),
        ) ?? new SiteSetting([
            'site_name' => 'Architecture Portfolio',
            'architect_name' => 'Architecture Studio',
            'professional_title' => 'Architectural design',
        ]);
    }

    /** @return Collection<int, Category> */
    public function categories(bool $featuredOnly = false): Collection
    {
        $cacheKey = $featuredOnly ? 'categories.featured' : 'categories';

        return $this->remember(
            $cacheKey,
            fn () => Category::query()
                ->where('is_active', true)
                ->when($featuredOnly, fn ($query) => $query->whereHas('projects', fn ($projects) => $projects->rootProjects()->where('is_featured', true)))
                ->withCount(['projects as main_projects_count' => fn ($query) => $query
                    ->whereNull('parent_id')
                    ->when($featuredOnly, fn ($projects) => $projects->where('is_featured', true))])
                ->with([
                    'projects' => fn ($query) => $query->rootProjects()
                        ->when($featuredOnly, fn ($projects) => $projects->where('is_featured', true))
                        ->with('coverImage')
                        ->with(['images' => fn ($images) => $images->limit(1)])
                        ->orderBy('sort_order'),
                ])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            new Collection(),
        );
    }

    /** @return Collection<int, Category> */
    public function categoryMenu(): Collection
    {
        return $this->remember(
            'categories.menu',
            fn () => Category::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            new Collection(),
        );
    }

    /** @return Collection<int, Project> */
    public function projects(?string $categorySlug = null, int $limit = 24, bool $featuredOnly = false): Collection
    {
        $categoryKey = $categorySlug ?: 'all';
        $featuredKey = $featuredOnly ? '.featured' : '';

        return $this->remember(
            "projects.{$categoryKey}.{$limit}{$featuredKey}",
            function () use ($categorySlug, $limit, $featuredOnly): Collection {
                $query = Project::query()
                    ->rootProjects()
                    ->when($featuredOnly, fn ($projects) => $projects->where('is_featured', true))
                    ->withCount('children')
                    ->with(['category', 'coverImage', 'images' => fn ($images) => $images->limit(1)])
                    ->orderBy('sort_order')
                    ->orderByDesc('year')
                    ->orderBy('id');

                if ($categorySlug) {
                    $query->whereHas('category', fn ($category) => $category->where('slug', $categorySlug)->where('is_active', true));
                }

                if ($limit > 0) {
                    $query->limit($limit);
                }

                return $query->get();
            },
            new Collection(),
        );
    }

    /** @return LengthAwarePaginator<int, Project> */
    public function projectsPage(?string $categorySlug = null, int $perPage = 24, int $page = 1): LengthAwarePaginator
    {
        $categoryKey = $categorySlug ?: 'all';

        return $this->remember(
            "projects-page.{$categoryKey}.{$perPage}.{$page}",
            function () use ($categorySlug, $perPage, $page): LengthAwarePaginator {
                $query = Project::query()
                    ->rootProjects()
                    ->withCount('children')
                    ->with(['category', 'coverImage', 'images' => fn ($images) => $images->limit(1)])
                    ->orderBy('sort_order')
                    ->orderByDesc('year')
                    ->orderBy('id');

                if ($categorySlug) {
                    $query->whereHas('category', fn ($category) => $category->where('slug', $categorySlug)->where('is_active', true));
                }

                return $query->paginate($perPage, ['*'], 'page', $page);
            },
            new LengthAwarePaginator([], 0, $perPage, $page, [
                'path' => request()->url(),
                'query' => request()->query(),
            ]),
        );
    }

    public function project(string $slug): ?Project
    {
        return $this->remember(
            "project.{$slug}",
            fn () => Project::query()
                ->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('parent_id')->orWhereHas('parent'))
                ->with(['category', 'parent.category'])
                ->withCount(['children', 'images'])
                ->first(),
            null,
        );
    }

    /** @return LengthAwarePaginator<int, Project> */
    public function projectChildrenPage(int $projectId, int $perPage = 12, int $page = 1): LengthAwarePaginator
    {
        return $this->remember(
            "project-children-page.{$projectId}.{$perPage}.{$page}",
            fn () => Project::query()
                ->where('parent_id', $projectId)
                ->with(['category', 'coverImage', 'images' => fn ($images) => $images->limit(1)])
                ->withCount('images')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->paginate($perPage, ['*'], 'subprojects_page', $page),
            $this->emptyPaginator($perPage, $page),
        );
    }

    /** @return LengthAwarePaginator<int, \App\Models\ProjectImage> */
    public function projectImagesPage(int $projectId, int $perPage = 12, int $page = 1): LengthAwarePaginator
    {
        return $this->remember(
            "project-images-page.{$projectId}.{$perPage}.{$page}",
            fn () => \App\Models\ProjectImage::query()
                ->where('project_id', $projectId)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->paginate($perPage, ['*'], 'gallery_page', $page),
            $this->emptyPaginator($perPage, $page),
        );
    }

    public function invalidate(): void
    {
        try {
            Cache::put('public-content-version', Str::uuid()->toString(), now()->addYear());
        } catch (Throwable) {
        }
    }

    private function remember(string $key, callable $resolve, mixed $fallback): mixed
    {
        try {
            $version = Cache::get('public-content-version', '1');
            $cacheKey = 'public-content.'.$version.'.'.$key;
            $stale = Cache::get($cacheKey);

            if ($stale !== null) {
                return $stale;
            }
        } catch (Throwable) {
            $cacheKey = null;
            $stale = null;
        }

        try {
            $content = $resolve();

            if ($cacheKey !== null) {
                try {
                    Cache::put($cacheKey, $content, now()->addMinutes(self::CACHE_TTL_MINUTES));
                } catch (Throwable) {
                }
            }

            return $content;
        } catch (Throwable $exception) {
            report($exception);

            return $stale ?? $fallback;
        }
    }

    private function emptyPaginator(int $perPage, int $page): LengthAwarePaginator
    {
        return new LengthAwarePaginator([], 0, $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
