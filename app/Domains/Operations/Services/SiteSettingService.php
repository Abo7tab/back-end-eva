<?php

declare(strict_types=1);

namespace App\Domains\Operations\Services;

use App\Domains\Operations\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettingService
{
    /**
     * Get all site settings (cached).
     */
    public function getAll(): mixed
    {
        return Cache::remember('site_settings_all', 3600, function () {
            return SiteSetting::all();
        });
    }

    /**
     * Get a single setting by key.
     */
    public function getByKey(string $key): ?SiteSetting
    {
        return SiteSetting::where('setting_key', $key)->first();
    }

    /**
     * Bulk update settings.
     */
    public function bulkUpdate(array $settings): void
    {
        foreach ($settings as $item) {
            SiteSetting::where('setting_key', $item['setting_key'])
                ->update(['setting_value' => $item['setting_value']]);
        }
        $this->flushCache();
    }

    /**
     * Flush settings cache.
     */
    private function flushCache(): void
    {
        Cache::forget('site_settings_all');
    }
}
