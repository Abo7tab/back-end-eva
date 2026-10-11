<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class AdmissionControl
{
    /**
     * Maximum concurrent in-flight requests processed at the same millisecond.
     * Tuned for 0.25 vCPU & 1GB RAM to prevent server overload and queue bloat.
     */
    private const MAX_CONCURRENT_REQUESTS = 20;

    public function handle(Request $request, Closure $next): Response
    {
        // Always give priority to checkout and admin operations
        if ($request->is('api/v1/checkout*') || $request->is('api/v1/admin*')) {
            return $next($request);
        }

        $inFlight = (int) Cache::get('server_inflight_count', 0);

        if ($inFlight >= self::MAX_CONCURRENT_REQUESTS) {
            return response()->json([
                'success' => false,
                'message' => 'الخادم مشغول حالياً بسبب كثافة الزوار، يرجى المحاولة بعد ثوانٍ قليلة.',
                'errors' => (object)[],
            ], 503, [
                'Retry-After' => '5',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
            ]);
        }

        // Increment counter
        Cache::increment('server_inflight_count');

        try {
            return $next($request);
        } finally {
            $current = (int) Cache::get('server_inflight_count', 1);
            if ($current > 0) {
                Cache::decrement('server_inflight_count');
            } else {
                Cache::put('server_inflight_count', 0, 15);
            }
        }
    }
}
