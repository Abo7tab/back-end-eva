<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Domains\OrderLogging\Services\OrderLogService;
use App\Domains\Operations\Models\SiteSetting;
use App\Domains\Catalog\Models\Product;

// Toggle images ON
SiteSetting::updateOrCreate(
    ['setting_key' => 'whatsapp_include_images'],
    ['setting_value' => 'true']
);

$product1 = Product::first();
$product2 = Product::skip(1)->first();

if (!$product1 || !$product2) {
    echo "Not enough products.";
    exit;
}

$service = app(OrderLogService::class);
$data = [
    'customer_name' => 'Ahmed Mohamed',
    'customer_phone' => '01012345678',
    'items' => [
        ['product_id' => $product1->id, 'quantity' => 2],
        ['product_id' => $product2->id, 'quantity' => 1]
    ]
];

$result = $service->checkout($data);
echo "\n==============================\n";
echo "GENERATED MESSAGE (Images ON):\n";
echo "==============================\n";
echo $result['order']->checkout_message;
echo "\n==============================\n";

// Toggle images OFF
SiteSetting::updateOrCreate(
    ['setting_key' => 'whatsapp_include_images'],
    ['setting_value' => 'false']
);

$resultOff = $service->checkout($data);
echo "\n==============================\n";
echo "GENERATED MESSAGE (Images OFF):\n";
echo "==============================\n";
echo $resultOff['order']->checkout_message;
echo "\n==============================\n";
