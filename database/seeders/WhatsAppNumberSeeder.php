<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Domains\Operations\Models\WhatsAppNumber;
use Illuminate\Database\Seeder;

class WhatsAppNumberSeeder extends Seeder
{
    public function run(): void
    {
        WhatsAppNumber::updateOrCreate(
            ['phone_number' => '+201111111111'],
            [
                'display_name' => 'Support Agent 1',
                'is_active' => true,
                'order_count' => 0,
                'last_assigned_at' => null,
            ]
        );

        WhatsAppNumber::updateOrCreate(
            ['phone_number' => '+201222222222'],
            [
                'display_name' => 'Support Agent 2',
                'is_active' => true,
                'order_count' => 0,
                'last_assigned_at' => null,
            ]
        );
    }
}
