<?php declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Admin;

use App\Domains\Admin\Resources\AdminResource;
use App\Domains\Admin\Services\AdminAuthService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    public function __construct(private AdminAuthService $service) {}

    public function login(\App\Http\Requests\Admin\LoginRequest $request)
    {
        $result = $this->service->login($request->validated('email'), $request->validated('password'))
            ?? abort(401, 'Invalid credentials');
        return response()->json([
            'success' => true,
            'data' => ['admin' => new AdminResource($result['admin']), 'token' => $result['token']],
            'message' => 'Logged in successfully',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => new AdminResource(\Illuminate\Support\Facades\Auth::guard('sanctum')->user()),
            'message' => 'OK',
        ]);
    }

    public function logout(Request $request)
    {
        $this->service->logout(\Illuminate\Support\Facades\Auth::guard('sanctum')->user());
        return response()->json(['success' => true, 'data' => null, 'message' => 'Logged out successfully']);
    }
}
