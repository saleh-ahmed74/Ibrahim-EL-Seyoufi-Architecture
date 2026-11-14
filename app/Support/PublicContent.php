<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Project;
use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Collection;
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
    public function categories(): Collection
    {
        return $this->remember(
            'categories',
            fn () => Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            new Collection(),
        );
    }

    /** @return Collection<int, Project> */
    public function projects(?string $categorySlug = null, int $limit = 24): Collection
    {
        $categoryKey = $categorySlug ?: 'all';

        return $this->remember(
            "projects.{$categoryKey}.{$limit}",
            function () use ($categorySlug, $limit): Collection {
                $query = Project::query()
                    ->rootProjects()
                    ->with(['category', 'images', 'coverImage', 'children.images', 'children.coverImage'])
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

    public function project(string $slug): ?Project
    {
        return $this->remember(
            "project.{$slug}",
            fn () => Project::query()
                ->where('slug', $slug)
                ->where(fn ($query) => $query->whereNull('parent_id')->orWhereHas('parent'))
                ->with([
                    'category',
                    'parent',
                    'images',
                    'coverImage',
                    'children.category',
                    'children.images',
                    'children.coverImage',
                ])
                ->first(),
            null,
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
}
