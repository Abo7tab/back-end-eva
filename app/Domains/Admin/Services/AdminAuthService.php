<?php

declare(strict_types=1);

namespace App\Domains\Admin\Services;

use App\Domains\Admin\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    /**
     * Attempt login and return token.
     *
     * Returns array with admin + token on success, null on failure.
     */
    public function login(string $email, string $password): ?array
    {
        $admin = Admin::where('email', $email)->first();

        if (!$admin || !Hash::check($password, $admin->password)) {
            return null;
        }

        // Sanctum abilities per §5.1
        $token = $admin->createToken(
            'admin-token',
            ['admin:read', 'admin:write', 'admin:delete'],
            now()->addHours(24)  // 24h expiry per §5.1
        );

        return [
            'admin' => $admin,
            'token' => $token->plainTextToken,
        ];
    }

    /**
     * Logout: revoke the current token.
     */
    public function logout(Admin $admin): void
    {
        $admin->currentAccessToken()->delete();
    }
}
