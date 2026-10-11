<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function index()
    {
        $current   = (int) Cache::get('server_inflight_count', 0);
        $maxLimit  = 20;

        return response()->json([
            'success' => true,
            'data'    => [
                'current_concurrent'         => $current,
                'max_concurrent_limit'       => $maxLimit,
                'concurrency_percentage'     => (int) round(($current / $maxLimit) * 100),
                'total_requests_served'      => (int) Cache::get('health_total_requests', 0),
                'rate_limit_hits_total'      => (int) Cache::get('health_rate_limit_hits', 0),
                'rate_limit_hits_today'      => (int) Cache::get('health_rate_limit_hits_today', 0),
                'admission_rejections_total' => (int) Cache::get('health_admission_rejections', 0),
                'admission_rejections_today' => (int) Cache::get('health_admission_rejections_today', 0),
                'last_request_at'            => Cache::get('health_last_request_at'),
                'php_version'                => PHP_VERSION,
                'laravel_version'            => app()->version(),
            ],
            'message' => 'OK',
        ]);
    }
}
