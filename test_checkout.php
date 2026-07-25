<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$product = \App\Domains\Catalog\Models\Product::first();
$uuid = $product->uuid;

$payload = [
    'customer_name' => 'Test User',
    'items' => [
        ['product_uuid' => $uuid, 'quantity' => 2]
    ]
];

$request = \Illuminate\Http\Request::create('/api/v1/checkout', 'POST', $payload);
$formRequest = \App\Http\Requests\OrderLogging\CheckoutRequest::createFrom($request);

$validator = \Illuminate\Support\Facades\Validator::make($request->all(), $formRequest->rules());

if ($validator->fails()) {
    echo "Validation failed:\n";
    print_r($validator->errors()->toArray());
} else {
    echo "Validation passed!\n";
    $service = app(\App\Domains\OrderLogging\Services\OrderLogService::class);
    $result = $service->checkout($request->all());
    echo "Result:\n";
    print_r($result);
}
