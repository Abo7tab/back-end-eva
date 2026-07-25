<?php

namespace App\Providers;

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
        \Illuminate\Support\Facades\RateLimiter::for('public', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('checkout', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('admin-login', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'Too many login attempts. Try again later.',
                        'errors'  => (object)[],
                    ], 429);
                });
        });

        \Illuminate\Support\Facades\RateLimiter::for('admin-crud', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60)->by($request->user('sanctum')?->id ?: $request->ip());
        });
    }
}
