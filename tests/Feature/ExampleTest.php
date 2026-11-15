<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\Category;
use App\Models\SiteSetting;
use App\Support\PublicContent;
use App\Support\HomepageCopy;
use App\Livewire\Admin\Dashboard;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use App\Livewire\Admin\Projects as AdminProjects;
use App\Livewire\Site\HomePage;
use App\Livewire\Site\CategoryMenu;
use App\Livewire\Site\ProjectsPage;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_public_and_account_pages_load_without_authentication(): void
    {
        foreach ([
            '/',
            '/projects',
            '/about',
            '/login',
            '/register',
            '/forgot-password',
            '/reset-password/invalid-token?email=test@example.com',
            '/admin/login',
            '/up',
        ] as $uri) {
            $this->get($uri)->assertSuccessful();
        }
    }

    public function test_unknown_project_slugs_return_not_found(): void
    {
        $this->get('/projects/missing-project')->assertNotFound();
    }

    public function test_admin_pages_require_an_authenticated_administrator(): void
    {
        foreach ([
            '/admin',
            '/admin/projects',
            '/admin/categories',
            '/admin/settings',
            '/admin/inquiries',
            '/admin/analytics',
        ] as $uri) {
            $this->get($uri)->assertRedirect('/login');
        }
    }

    public function test_public_responses_include_security_headers(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_offline_fallback_identifies_offline_state_in_english_and_arabic(): void
    {
        $offlinePage = file_get_contents(public_path('offline.html'));
        $serviceWorker = file_get_contents(public_path('service-worker.js'));

        $this->assertIsString($offlinePage);
        $this->assertIsString($serviceWorker);
        $this->assertStringContainsString('Offline mode · site unavailable', $offlinePage);
        $this->assertStringContainsString('وضع عدم الاتصال · الموقع غير متاح', $offlinePage);
        $this->assertStringContainsString('Try again', $offlinePage);
        $this->assertStringContainsString('navigator.onLine', $offlinePage);
        $this->assertStringContainsString('portfolio-public-pages-v2', $serviceWorker);
    }

    public function test_project_detail_view_paginates_subprojects_and_gallery_images(): void
    {
        $project = new Project([
            'title' => 'Pagination test project',
            'slug' => 'pagination-test-project',
            'description' => 'Test description',
        ]);
        $project->setAttribute('children_count', 13);
        $project->setAttribute('images_count', 13);
        $project->setRelation('category', null);
        $project->setRelation('parent', null);

        $children = collect(range(1, 12))->map(function (int $index): Project {
            $child = new Project([
                'title' => "Test subproject {$index}",
                'slug' => "test-subproject-{$index}",
                'description' => 'Test subproject description',
            ]);
            $child->setRelation('category', null);
            $child->setRelation('coverImage', null);
            $child->setRelation('images', collect());

            return $child;
        });
        $images = collect(range(1, 12))->map(fn (int $index) => new ProjectImage([
            'image_path' => "projects/test-{$index}.webp",
            'alt_text' => "Test view {$index}",
        ]));

        $html = view('livewire.site.project-page', [
            'project' => $project,
            'children' => new LengthAwarePaginator($children, 13, 12, 1, [
                'path' => '/projects/pagination-test-project',
                'pageName' => 'subprojects_page',
            ]),
            'images' => new LengthAwarePaginator($images, 13, 12, 1, [
                'path' => '/projects/pagination-test-project',
                'pageName' => 'gallery_page',
            ]),
        ])->render();

        $this->assertStringContainsString('Subproject pages', $html);
        $this->assertStringContainsString('Gallery pages', $html);
        $this->assertStringContainsString('Zoom image 1: Test view 1', $html);
        $this->assertStringContainsString('photo-lightbox', $html);
        $this->assertStringContainsString('project-feature-preview-previous', $html);
        $this->assertStringContainsString('project-feature-preview-next', $html);
        $this->assertStringContainsString('x-on:pointerup="endDrag($event)"', $html);
        $this->assertStringNotContainsString('class="project-feature-controls"', $html);
    }

    public function test_homepage_requests_featured_projects_and_new_projects_start_unfeatured(): void
    {
        $this->assertFalse((new Project())->is_featured);
        $this->assertFalse((new AdminProjects())->isFeatured);

        $content = \Mockery::mock(PublicContent::class);
        $content->shouldReceive('settings')->once()->andReturn(new SiteSetting([
            'site_name' => 'Test studio',
            'architect_name' => 'Test architect',
            'professional_title' => 'Architecture',
            'hero_image' => 'hero/person.webp',
            'homepage_copy' => array_replace_recursive(HomepageCopy::defaults(), [
                'hero_eyebrow' => ['en' => 'Custom homepage headline', 'ar' => 'عنوان رئيسي مخصص'],
            ]),
        ]));
        $content->shouldReceive('projects')->once()->with(null, 6, true)->andReturn(new \Illuminate\Database\Eloquent\Collection());
        $this->app->instance(PublicContent::class, $content);

        Livewire::test(HomePage::class)
            ->assertSee('All projects')
            ->assertSee('Custom homepage headline')
            ->assertSeeHtml('data-ar="عنوان رئيسي مخصص"')
            ->assertSeeHtml('class="home-hero-person"')
            ->assertDontSee('Explore by discipline')
            ->assertSeeHtml('home-projects-grid');
    }

    public function test_every_homepage_copy_setting_has_english_and_arabic_defaults(): void
    {
        $defaults = HomepageCopy::defaults();

        $this->assertSame(array_keys($defaults), array_keys(HomepageCopy::labels()));

        foreach ($defaults as $translation) {
            $this->assertNotEmpty($translation['en']);
            $this->assertNotEmpty($translation['ar']);
        }
    }

    public function test_home_about_band_is_removed_and_about_link_sits_with_footer_logo(): void
    {
        $homepage = file_get_contents(resource_path('views/livewire/site/home-page.blade.php'));
        $layout = file_get_contents(resource_path('views/layouts/public.blade.php'));

        $this->assertIsString($homepage);
        $this->assertIsString($layout);
        $this->assertStringNotContainsString('about-band', $homepage);
        $this->assertStringContainsString('footer-brand-about', $layout);
        $this->assertStringContainsString('footer-logo', $layout);
        $this->assertStringContainsString("route('about')", $layout);
    }

    public function test_categories_load_only_when_the_menu_opens_and_projects_page_has_no_category_rail(): void
    {
        $menu = Livewire::test(CategoryMenu::class)
            ->assertSee('Loading categories…')
            ->assertSet('loaded', false);
        $menuSource = file_get_contents(app_path('Livewire/Site/CategoryMenu.php'));
        $projectsPage = file_get_contents(resource_path('views/livewire/site/projects-page.blade.php'));
        $projectsSource = file_get_contents(app_path('Livewire/Site/ProjectsPage.php'));

        $this->assertIsString($menuSource);
        $this->assertIsString($projectsPage);
        $this->assertIsString($projectsSource);
        $this->assertStringContainsString('categoryMenu()', $menuSource);
        $this->assertStringContainsString('if ($this->loaded)', $menuSource);
        $this->assertStringNotContainsString('category-filter', $projectsPage);
        $this->assertStringNotContainsString('->categories()', $projectsSource);
        $this->assertStringContainsString('projectsPage($this->category ?: null', $projectsSource);
    }

    public function test_category_menu_runs_its_category_loader_only_once(): void
    {
        $content = \Mockery::mock(PublicContent::class);
        $content->shouldReceive('categoryMenu')->once()->andReturn(new \Illuminate\Database\Eloquent\Collection([
            new Category(['name' => 'Residential', 'slug' => 'residential']),
        ]));
        $this->app->instance(PublicContent::class, $content);

        Livewire::test(CategoryMenu::class)
            ->assertSet('loaded', false)
            ->call('loadCategories')
            ->assertSet('loaded', true)
            ->assertSee('Residential')
            ->call('loadCategories')
            ->assertSee('Residential');
    }

    public function test_public_content_reuses_cached_results_until_the_cache_version_changes(): void
    {
        Cache::put('public-content-version', $version = (string) \Illuminate\Support\Str::uuid());
        $calls = 0;
        $resolver = function () use (&$calls): array {
            $calls++;

            return ['result' => 'cached'];
        };
        $remember = new \ReflectionMethod(PublicContent::class, 'remember');

        $first = $remember->invoke(new PublicContent(), 'cache-test', $resolver, []);
        $second = $remember->invoke(new PublicContent(), 'cache-test', $resolver, []);

        $this->assertSame(['result' => 'cached'], $first);
        $this->assertSame($first, $second);
        $this->assertSame(1, $calls);
        $this->assertNotEmpty($version);
    }

    public function test_admin_dashboard_remains_default_and_admin_theme_toggle_uses_light_by_default(): void
    {
        $dashboardRoute = Route::getRoutes()->getByName('admin.dashboard');
        $adminLayout = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $adminLoginLayout = file_get_contents(resource_path('views/layouts/admin-login.blade.php'));

        $this->assertNotNull($dashboardRoute);
        $this->assertSame(Dashboard::class, $dashboardRoute->getActionName());
        $this->assertIsString($adminLayout);
        $this->assertIsString($adminLoginLayout);
        $this->assertStringContainsString('data-theme="light"', $adminLayout);
        $this->assertStringContainsString('data-theme-default="light"', $adminLayout);
        $this->assertStringContainsString('data-theme-storage="admin_theme_v2"', $adminLayout);
        $this->assertStringContainsString('>Dark mode</button>', $adminLayout);
        $this->assertStringContainsString('data-theme-default="light"', $adminLoginLayout);
        $this->assertStringContainsString('data-theme-storage="admin_theme_v2"', $adminLoginLayout);
    }

    public function test_admin_database_actions_have_dismissible_auto_hiding_feedback(): void
    {
        $adminLayout = file_get_contents(resource_path('views/layouts/admin.blade.php'));
        $adminComponent = file_get_contents(app_path('Livewire/Admin/AdminComponent.php'));

        $this->assertIsString($adminLayout);
        $this->assertIsString($adminComponent);
        $this->assertStringContainsString('admin-action-feedback.window', $adminLayout);
        $this->assertStringContainsString('setTimeout(() => this.open = false, 5000)', $adminLayout);
        $this->assertStringContainsString('Close notification', $adminLayout);
        $this->assertStringContainsString("dispatch('admin-action-feedback'", $adminComponent);
    }

}
