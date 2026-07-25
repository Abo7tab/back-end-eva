<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/api/v1/admin/settings', 'GET');
$request->headers->set('Accept', 'application/json');

// We need to bypass auth or just run the controller method directly to see how ForceJsonResponse wraps it
// Actually, let's just create a new ForceJsonResponse and pass a dummy response
$middleware = new \App\Http\Middleware\ForceJsonResponse();

$response = new \Illuminate\Http\JsonResponse([
    'data' => [
        ['id' => 1, 'setting_key' => 'site_name', 'setting_value' => 'Eva Beauty']
    ]
], 200);

$dummyRequest = Illuminate\Http\Request::create('/api/v1/admin/settings', 'GET');
$dummyRequest->headers->set('Accept', 'application/json');

$finalResponse = $middleware->handle($dummyRequest, function() use ($response) {
    return $response;
});

echo "Final Content: " . $finalResponse->getContent() . "\n";
