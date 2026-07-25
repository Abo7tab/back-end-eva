<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Domains\Operations\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ThemeController extends Controller
{
    /**
     * Get theme configuration.
     * Allowed themes: default, women, men.
     */
    public function show(string $theme): JsonResponse
    {
        if (!in_array($theme, ['default', 'women', 'men'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Theme not found.',
                'errors' => (object)[],
            ], 404);
        }

        $cacheKey = "theme_config_{$theme}";

        $themeData = Cache::remember($cacheKey, 3600, function () use ($theme) {
            $settings = SiteSetting::where('setting_key', 'LIKE', "theme_{$theme}_%")->get()->pluck('setting_value', 'setting_key');

            return [
                'theme' => $theme,
                'colors' => [
                    'primary' => $settings->get("theme_{$theme}_primary_color", '#F97316'),
                    'secondary' => $settings->get("theme_{$theme}_secondary_color", '#FFFFFF'),
                    'accent' => $settings->get("theme_{$theme}_accent_color", '#FED7AA'),
                    'text' => $settings->get("theme_{$theme}_text_color", '#1F2937'),
                ],
                'logo_url' => $settings->get("theme_{$theme}_logo_url"),
                'background' => [
                    'color_1' => $settings->get("theme_{$theme}_bg_color_1", '#F97316'),
                    'color_2' => $settings->get("theme_{$theme}_bg_color_2", '#FB923C'),
                    'animation_speed' => (int) $settings->get("theme_{$theme}_animation_speed", 50),
                    'animation_blur' => (int) $settings->get("theme_{$theme}_animation_blur", 80),
                    'animation_opacity' => (int) $settings->get("theme_{$theme}_animation_opacity", 60),
                    'animation_blobs_count' => (int) $settings->get("theme_{$theme}_animation_blobs_count", 4),
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $themeData,
            'message' => 'OK',
        ]);
    }
}
