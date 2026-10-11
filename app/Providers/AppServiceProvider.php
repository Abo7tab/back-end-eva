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
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(200)->by($request->ip())
                ->response(function () use ($request) {
                    \Illuminate\Support\Facades\Log::warning('Public rate limit hit', [
                        'ip'  => $request->ip(),
                        'url' => $request->fullUrl(),
                    ]);

                    // Track rate limit stats for health monitor
                    \Illuminate\Support\Facades\Cache::increment('health_rate_limit_hits');
                    \Illuminate\Support\Facades\Cache::put(
                        'health_rate_limit_hits_today',
                        \Illuminate\Support\Facades\Cache::increment('health_rate_limit_hits_today'),
                        86400
                    );
                    $now = now()->setTimezone('Africa/Cairo');
                    \Illuminate\Support\Facades\Cache::increment('traffic_h_' . $now->format('Y-m-d_H') . '_429');
                    \Illuminate\Support\Facades\Cache::increment('traffic_d_' . $now->format('Y-m-d') . '_429');

                    return response()->json([
                        'success' => false,
                        'message' => 'يرجى الانتظار بضع ثوانٍ قبل المتابعة.',
                        'errors'  => (object)[],
                    ], 429, ['Retry-After' => '30']);
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
