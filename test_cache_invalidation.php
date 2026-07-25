<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Api\V1\Operations\SiteSettingController;
use App\Http\Requests\Operations\UpdateSiteSettingsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

$request = UpdateSiteSettingsRequest::create('/api/v1/admin/settings/any', 'PUT', [
    'settings' => [
        ['setting_key' => 'theme_women_primary_color', 'setting_value' => '#000000']
    ]
]);

$request->setValidator(Validator::make($request->all(), [
    'settings' => 'required|array',
    'settings.*.setting_key' => 'required|string|exists:site_settings,setting_key',
    'settings.*.setting_value' => 'nullable|string',
]));

$controller = app(SiteSettingController::class);
$controller->update($request, 'any');

$httpKernel = app(\Illuminate\Contracts\Http\Kernel::class);
$req = Request::create('/api/v1/public/theme/women', 'GET');
$response = $httpKernel->handle($req);

echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
