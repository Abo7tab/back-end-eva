<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Domains\Admin\Models\Admin;
use Laravel\Sanctum\Sanctum;

$admin = Admin::first();
Sanctum::actingAs($admin, ['*']);

$request = Request::create('/api/v1/admin/whatsapp-numbers', 'POST', []);
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . PHP_EOL;
echo "Content: " . $response->getContent() . PHP_EOL;
