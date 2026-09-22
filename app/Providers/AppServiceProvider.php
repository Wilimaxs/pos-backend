<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for(
            'api',
            fn(Request $request) => Limit::perMinute(60)
                ->by($request->user()?->getAuthIdentifier() ?? $request->ip())
        );
        RateLimiter::for(
            'login',
            function (Request $request) {
                $phone = $request->input('phone');
                return Limit::perMinute(5)->by(
                    $request->ip() . '|' .
                    (is_string($phone) ? trim($phone) : 'invalid')
                );
            }
        );
    }
}
