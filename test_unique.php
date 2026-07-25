<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;
use App\Domains\Admin\Models\Admin;
use App\Domains\Operations\Models\WhatsAppNumber;
use Laravel\Sanctum\Sanctum;

$admin = Admin::first();
Sanctum::actingAs($admin, ['*']);

// Ensure we have a number to duplicate
$firstNumber = WhatsAppNumber::first();
if (!$firstNumber) {
    echo "No numbers exist to test duplication.";
    exit;
}

$request = Request::create('/api/v1/admin/whatsapp-numbers', 'POST', [
    'phone_number' => $firstNumber->phone_number
]);
$request->headers->set('Accept', 'application/json');

$response = app()->handle($request);
echo "Status: " . $response->getStatusCode() . PHP_EOL;
echo "Content: " . $response->getContent() . PHP_EOL;
