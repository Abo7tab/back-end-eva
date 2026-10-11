<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        Model::preventLazyLoading(! $this->app->isProduction());

        \Illuminate\Support\Facades\RateLimiter::for('public', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(50)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'لقد قمت بإرسال عدد كبير من الطلبات، يرجى الانتظار دقيقة والمحاولة مجدداً.',
                        'errors'  => (object)[],
                    ], 429, ['Retry-After' => '60']);
                });
        });

        \Illuminate\Support\Facades\RateLimiter::for('checkout', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip())
                ->response(function () {
                    return response()->json([
                        'success' => false,
                        'message' => 'يرجى الانتظار دقيقة قبل إرسال طلب جديد.',
                        'errors'  => (object)[],
                    ], 429, ['Retry-After' => '60']);
                });
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
