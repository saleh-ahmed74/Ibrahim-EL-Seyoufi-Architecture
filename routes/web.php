<?php

use App\Http\Controllers\AdminLogoutController;
use App\Livewire\Admin\Analytics;
use App\Livewire\Admin\Categories;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Inquiries;
use App\Livewire\Admin\Login;
use App\Livewire\Admin\Projects;
use App\Livewire\Admin\SiteSettings;
use App\Livewire\Site\AboutPage;
use App\Livewire\Site\HomePage;
use App\Livewire\Site\ProjectPage;
use App\Livewire\Site\ProjectsPage;
use Illuminate\Support\Facades\Route;

Route::get('/', HomePage::class)->name('home');
Route::get('/projects', ProjectsPage::class)->name('projects.index');
Route::get('/projects/{slug}', ProjectPage::class)->name('projects.show');
Route::get('/about', AboutPage::class)->name('about');

Route::get('/admin/login', Login::class)->middleware('throttle:admin-login')->name('login');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'throttle:admin'])->group(function (): void {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/projects', Projects::class)->name('projects');
    Route::get('/categories', Categories::class)->name('categories');
    Route::get('/settings', SiteSettings::class)->name('settings');
    Route::get('/inquiries', Inquiries::class)->name('inquiries');
    Route::get('/analytics', Analytics::class)->name('analytics');
    Route::post('/logout', AdminLogoutController::class)->name('logout');
});
