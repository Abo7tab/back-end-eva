<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Http\Request;

$request = Request::create('/api/v1/admin/users/1', 'DELETE');
$user = $request->user();
var_dump($user);
