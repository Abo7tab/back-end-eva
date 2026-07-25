<?php

namespace Database\Seeders;

use App\Domains\OrderLogging\Models\OrderLog;
use App\Domains\OrderLogging\Models\OrderLogItem;
use App\Domains\Operations\Models\WhatsAppNumber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderLogsSeeder extends Seeder
{
    public function run(): void
    {
        $wa = WhatsAppNumber::first();
        if (!$wa) {
            $wa = WhatsAppNumber::create([
                'phone_number' => '+201001234567',
                'display_name' => 'خدمة العملاء',
                'is_active' => true,
            ]);
        }

        $statuses = ['pending', 'redirected', 'completed', 'failed'];
        $customers = [
            ['name' => 'أحمد محمد', 'phone' => '01012345678'],
            ['name' => 'سارة علي', 'phone' => '01123456789'],
            ['name' => 'محمود مصطفى', 'phone' => '01234567890'],
            ['name' => 'فاطمة إبراهيم', 'phone' => '01555555555'],
            ['name' => 'عمر خالد', 'phone' => '01099998888'],
        ];

        foreach ($customers as $i => $c) {
            $status = $statuses[$i % count($statuses)];
            $order = OrderLog::create([
                'reference_code' => 'GB-' . now()->format('Ym') . '-' . str_pad((string)($i + 1), 5, '0', STR_PAD_LEFT),
                'customer_name' => $c['name'],
                'customer_phone' => $c['phone'],
                'total_items' => 2,
                'subtotal' => 500.00,
                'total_price' => 500.00,
                'whatsapp_number_id' => $wa->id,
                'assigned_phone_number' => $wa->phone_number,
                'checkout_message' => "طلب جديد\nالاسم: {$c['name']}\nالإجمالي: 500 ج.م",
                'status' => $status,
                'created_at' => now()->subDays(rand(0, 5)),
            ]);

            OrderLogItem::create([
                'order_log_id' => $order->id,
                'product_id' => 1,
                'product_name_snapshot' => 'كريم مرطب',
                'product_slug_snapshot' => 'moisturizer-cream',
                'quantity' => 1,
                'unit_price' => 250.00,
                'line_total' => 250.00,
            ]);

            OrderLogItem::create([
                'order_log_id' => $order->id,
                'product_id' => 2,
                'product_name_snapshot' => 'غسول وجه',
                'product_slug_snapshot' => 'face-wash',
                'quantity' => 1,
                'unit_price' => 250.00,
                'line_total' => 250.00,
            ]);
        }
    }
}
