<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\Api\V1\Admin\AdminController;
use Illuminate\Http\Request;
use App\Domains\Admin\Models\Admin;

try {
    $admin1 = Admin::firstOrCreate(['email'=>'admin1@test.com'], ['name'=>'Admin 1', 'password'=>bcrypt('password')]);
    $admin2 = Admin::firstOrCreate(['email'=>'admin2@test.com'], ['name'=>'Admin 2', 'password'=>bcrypt('password')]);
    
    $request = Request::create('/api/v1/admin/users/' . $admin2->id, 'DELETE');
    $request->setUserResolver(function() use ($admin1) { return $admin1; });
    
    $controller = app(AdminController::class);
    $response = $controller->destroy($request, $admin2->id);
    
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
