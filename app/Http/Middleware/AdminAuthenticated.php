<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Domains\Admin\Models\Admin;

class AdminAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('sanctum')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
                'errors' => (object)[],
            ], 401);
        }

        $user = Auth::guard('sanctum')->user();

        if (!$user instanceof \App\Domains\Admin\Models\Admin) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
                'errors' => (object)[],
            ], 403);
        }

        return $next($request);
    }
}
