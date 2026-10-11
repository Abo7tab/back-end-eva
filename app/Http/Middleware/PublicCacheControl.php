<?php declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PublicCacheControl
{
    /**
     * Add public caching headers to safe GET requests and strip sensitive cookies.
     *
     * @param int $maxAge Browser cache time in seconds (default: 60s)
     * @param int $sMaxAge Shared CDN/Proxy cache time in seconds (default: 300s = 5m)
     * @param int $staleRevalidate Grace period to serve stale content while revalidating (default: 600s = 10m)
     */
    public function handle(
        Request $request,
        Closure $next,
        int $maxAge = 60,
        int $sMaxAge = 300,
        int $staleRevalidate = 600
    ): Response {
        $response = $next($request);

        // Only cache successful public GET / HEAD requests
        if (in_array($request->method(), ['GET', 'HEAD'], true) && $response->isSuccessful()) {
            $response->headers->set(
                'Cache-Control',
                "public, max-age={$maxAge}, s-maxage={$sMaxAge}, stale-while-revalidate={$staleRevalidate}"
            );

            // Strip any Set-Cookie headers so CDNs like Cloudflare never bypass caching
            $response->headers->remove('Set-Cookie');
        }

        return $response;
    }
}
