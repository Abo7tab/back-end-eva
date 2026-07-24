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
        $this->service->bulkUpdate($request->validated('settings'));
        return response()->json(['success' => true, 'data' => null, 'message' => 'Settings updated']);
    }
}
