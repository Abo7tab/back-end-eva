<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$httpKernel = app(\Illuminate\Contracts\Http\Kernel::class);

$user = App\Domains\Admin\Models\Admin::first();
if (!$user) {
    $user = App\Domains\Admin\Models\Admin::factory()->create();
}
$token = $user->createToken('test')->plainTextToken;

$req = Illuminate\Http\Request::create('/api/v1/admin/settings', 'GET');
$req->headers->set('Authorization', 'Bearer ' . $token);
$req->headers->set('Accept', 'application/json');

$response = $httpKernel->handle($req);

echo $response->getContent();
