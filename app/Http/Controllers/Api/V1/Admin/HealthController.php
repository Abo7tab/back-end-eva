<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Get real-time health metrics + lightweight 24h & 7d history from cache.
     * All times formatted in Africa/Cairo (Egypt) 12-hour format.
     */
    public function index()
    {
        $current  = (int) Cache::get('server_inflight_count', 0);
        $maxLimit = 20;

        // Build last 24 hours breakdown in Egypt Time (Africa/Cairo)
        $hourly = [];
        for ($i = 23; $i >= 0; $i--) {
            $dt = now('Africa/Cairo')->subHours($i);
            $hKey = $dt->format('Y-m-d_H');
            
            // Format 12-hour time with Arabic am/pm (e.g. "05:00 م", "09:00 ص")
            $hour12 = (int) $dt->format('g');
            $period = $dt->format('A') === 'AM' ? 'ص' : 'م';
            $timeFormatted = sprintf('%02d:00 %s', $hour12, $period);

            $hourly[] = [
                'time'         => $timeFormatted,
                'raw_hour'     => (int) $dt->format('H'),
                'date'         => $dt->format('Y-m-d'),
                'served'       => (int) Cache::get("traffic_h_{$hKey}_served", 0),
                'rate_limited' => (int) Cache::get("traffic_h_{$hKey}_429", 0),
                'shed_503'     => (int) Cache::get("traffic_h_{$hKey}_shed", 0),
            ];
        }

        // Build last 7 days breakdown
        $daily = [];
        for ($i = 6; $i >= 0; $i--) {
            $dt = now('Africa/Cairo')->subDays($i);
            $dKey = $dt->format('Y-m-d');
            
            $dayNameArabic = match ($dt->format('D')) {
                'Sat' => 'السبت',
                'Sun' => 'الأحد',
                'Mon' => 'الإثنين',
                'Tue' => 'الثلاثاء',
                'Wed' => 'الأربعاء',
                'Thu' => 'الخميس',
                'Fri' => 'الجمعة',
                default => $dt->format('D'),
            };

            $daily[] = [
                'day_name'     => $dayNameArabic,
                'date'         => $dt->format('m/d'),
                'full_date'    => $dt->format('Y-m-d'),
                'served'       => (int) Cache::get("traffic_d_{$dKey}_served", 0),
                'rate_limited' => (int) Cache::get("traffic_d_{$dKey}_429", 0),
                'shed_503'     => (int) Cache::get("traffic_d_{$dKey}_shed", 0),
            ];
        }

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
                'hourly_history'             => $hourly,
                'daily_history'              => $daily,
                'php_version'                => PHP_VERSION,
                'laravel_version'            => app()->version(),
            ],
            'message' => 'OK',
        ]);
    }

    /**
     * Reset stuck counters and today stats.
     */
    public function reset()
    {
        Cache::put('server_inflight_count', 0, 15);
        Cache::put('health_rate_limit_hits_today', 0, 86400);
        Cache::put('health_admission_rejections_today', 0, 86400);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'تم تصفير عدادات الضغط بنجاح',
        ]);
    }

    /**
     * Clear application cache (flush).
     */
    public function clearCache()
    {
        Cache::flush();
        Cache::put('server_inflight_count', 0, 15);

        return response()->json([
            'success' => true,
            'data'    => null,
            'message' => 'تم تفريغ كاش السيرفر بالكامل بنجاح',
        ]);
    }
}
