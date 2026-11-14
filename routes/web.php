<?php

use App\Http\Controllers\AdminLogoutController;
use App\Http\Controllers\AuthenticatedLogoutController;
use App\Http\Controllers\VerifyEmailController;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login as AccountLogin;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\VerifyEmail;
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

Route::get('/login', AccountLogin::class)->middleware(['guest', 'throttle:public-login'])->name('login');
Route::get('/register', Register::class)->middleware(['guest', 'throttle:register'])->name('register');
Route::get('/forgot-password', ForgotPassword::class)->middleware(['guest', 'throttle:password-forgot'])->name('password.request');
Route::get('/reset-password/{token}', ResetPassword::class)->middleware(['guest', 'throttle:password-reset'])->name('password.reset');
Route::get('/verify-email', VerifyEmail::class)->middleware(['auth', 'throttle:verification-notice'])->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', VerifyEmailController::class)->middleware(['auth', 'signed', 'throttle:verification-verify'])->name('verification.verify');
Route::get('/account/password', ChangePassword::class)->middleware(['auth', 'throttle:auth'])->name('account.password');
Route::post('/logout', AuthenticatedLogoutController::class)->middleware('auth')->name('logout');

Route::get('/admin/login', Login::class)->middleware('throttle:admin-login')->name('admin.login');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin', 'throttle:admin'])->group(function (): void {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/projects', Projects::class)->name('projects');
    Route::get('/categories', Categories::class)->name('categories');
    Route::get('/settings', SiteSettings::class)->name('settings');
    Route::get('/inquiries', Inquiries::class)->name('inquiries');
    Route::get('/analytics', Analytics::class)->name('analytics');
    Route::post('/logout', AdminLogoutController::class)->name('logout');
});
