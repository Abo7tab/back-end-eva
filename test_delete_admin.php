<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $admin = App\Domains\Admin\Models\Admin::create(['name'=>'Test', 'email'=>'test3@test.com', 'password'=>bcrypt('password')]);
    $admin->delete();
    echo "Deleted successfully\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
