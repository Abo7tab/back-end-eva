<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Operations;

use App\Domains\Operations\Resources\SiteSettingResource;
use App\Domains\Operations\Services\SiteSettingService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Operations\UpdateSiteSettingsRequest;

class SiteSettingController extends Controller
{
    public function __construct(private SiteSettingService $service) {}

    public function index()
    {
        return SiteSettingResource::collection($this->service->getAll());
    }

    public function showAdmin(string $key)
    {
        $setting = $this->service->getByKey($key) ?? abort(404, 'Setting not found');
        return new SiteSettingResource($setting);
    }

    public function update(UpdateSiteSettingsRequest $request, string $key)
    {
        // This exists for single-key updates if needed, though bulkUpdate is called.
        // It's better to keep it for backwards compatibility if any client uses it.
        $settings = $request->validated('settings');
        $this->service->bulkUpdate($settings);
        return response()->json(['success' => true, 'data' => null, 'message' => 'Settings updated']);
    }

    public function batchUpdate(UpdateSiteSettingsRequest $request)
    {
        $settings = $request->validated('settings');
        $this->service->bulkUpdate($settings);

        foreach ($settings as $setting) {
            $settingKey = $setting['setting_key'];
            if (str_starts_with($settingKey, 'theme_')) {
                // Extracts the theme name (e.g., 'default' from 'theme_default_primary_color')
                $parts = explode('_', $settingKey);
                if (isset($parts[1])) {
                    \Illuminate\Support\Facades\Cache::forget("theme_config_{$parts[1]}");
                }
            }
        }

        return response()->json(['success' => true, 'data' => null, 'message' => 'Settings batch updated successfully']);
    }
}
