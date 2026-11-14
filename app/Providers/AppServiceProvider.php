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
    }
}
