<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        Livewire::addPersistentMiddleware(ThrottleRequests::class);

        if ($this->app->isProduction()) {
            URL::forceScheme('https');
            URL::forceRootUrl(config('app.url'));
        }

        RateLimiter::for('admin-login', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('admin', fn (Request $request): Limit => Limit::perMinute(120)->by($request->user()?->id.'|'.$request->ip()));

        RateLimiter::for('public-login', fn (Request $request): Limit => Limit::perMinute(30)->by($request->ip()));

        RateLimiter::for('register', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('password-forgot', fn (Request $request): Limit => Limit::perMinute(3)->by($request->ip()));

        RateLimiter::for('password-reset', fn (Request $request): Limit => Limit::perMinute(5)->by($request->ip()));

        RateLimiter::for('verification-notice', fn (Request $request): Limit => Limit::perMinute(6)->by($request->user()?->id.'|'.$request->ip()));

        RateLimiter::for('verification-verify', fn (Request $request): Limit => Limit::perMinute(6)->by($request->user()?->id.'|'.$request->ip()));

        RateLimiter::for('auth', fn (Request $request): Limit => Limit::perMinute(60)->by($request->user()?->id.'|'.$request->ip()));
    }
}
