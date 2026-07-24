<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Admin\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * WARNING: Default password. MUST be changed after first login in production.
     */
    public function run(): void
    {
        Admin::updateOrCreate(
            ['email' => 'admin@evabeautyhub.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'password')),
            ]
        );
    }
}
