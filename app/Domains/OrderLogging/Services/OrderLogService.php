<?php

declare(strict_types=1);

namespace App\Domains\OrderLogging\Services;

use App\Domains\Catalog\Models\Product;
use App\Domains\Operations\Models\SiteSetting;
use App\Domains\Operations\Services\WhatsAppRoutingService;
use App\Domains\OrderLogging\Models\OrderLog;
use App\Domains\OrderLogging\Models\OrderLogItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderLogService
{
    public function __construct(
        private WhatsAppRoutingService $whatsAppRoutingService,
    ) {}

    /**
     * Process checkout: create order log + items, assign WhatsApp number.
     *
     * Per §5.2: Never trust client-supplied price or total — recalculate
     * server-side from the DB price column.
     *
     * Per Known Open Decision #1: line_total is computed server-side
     * (quantity × unit_price from DB) to prevent drift.
     */
    public function checkout(array $data): array
    {
        // 1. Assign WhatsApp number (race-condition safe)
        $whatsAppNumber = $this->whatsAppRoutingService->assignNextNumber();

        if ($whatsAppNumber === null) {
            abort(503, 'No active WhatsApp number available. Please try again later.');
        }

        return DB::transaction(function () use ($data, $whatsAppNumber) {
            // 2. Load products from DB and compute prices server-side
            $items = [];
            $subtotal = '0.00';
            $totalItems = 0;

            foreach ($data['items'] as $cartItem) {
                $product = Product::findOrFail($cartItem['product_id']);
                $quantity = (int) $cartItem['quantity'];
                $unitPrice = $product->price;
                $lineTotal = bcmul((string) $unitPrice, (string) $quantity, 2);

                $items[] = [
                    'product_id'             => $product->id,
                    'product_name_snapshot'   => $product->name,
                    'product_slug_snapshot'   => $product->slug,
                    'product_image_snapshot'  => $product->cover_image_url,
                    'quantity'               => $quantity,
                    'unit_price'             => $unitPrice,
                    'line_total'             => $lineTotal,
                ];

                $subtotal = bcadd($subtotal, $lineTotal, 2);
                $totalItems += $quantity;
            }

            // 3. Generate reference code
            $referenceCode = $this->generateReferenceCode();

            // 4. Create order log
            $orderLog = OrderLog::create([
                'reference_code'        => $referenceCode,
                'customer_name'         => $data['customer_name'] ?? null,
                'customer_phone'        => $data['customer_phone'] ?? null,
                'total_items'           => $totalItems,
                'subtotal'              => $subtotal,
                'total_price'           => $subtotal,
                'whatsapp_number_id'    => $whatsAppNumber->id,
                'assigned_phone_number' => $whatsAppNumber->phone_number,
                'status'                => 'pending',
            ]);

            // 5. Create order log items
            foreach ($items as $item) {
                $item['order_log_id'] = $orderLog->id;
                OrderLogItem::create($item);
            }

            // 6. Generate WhatsApp checkout message
            $message = $this->buildCheckoutMessage(
                $orderLog,
                $items,
                $data['customer_name'] ?? ''
            );

            $orderLog->update([
                'checkout_message' => $message,
                'status'           => 'redirected',
            ]);

            // 7. Build WhatsApp redirect URL
            $whatsappUrl = $this->buildWhatsAppUrl(
                $whatsAppNumber->phone_number,
                $message
            );

            return [
                'order'        => $orderLog->load('items'),
                'whatsapp_url' => $whatsappUrl,
            ];
        });
    }

    /**
     * List all orders (admin, paginated).
     */
    public function listForAdmin(int $perPage = 20): LengthAwarePaginator
    {
        return OrderLog::withTrashed()
            ->with(['items', 'whatsappNumber'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find an order log by ID (admin).
     */
    public function findById(int $id): ?OrderLog
    {
        return OrderLog::withTrashed()
            ->with(['whatsappNumber', 'items.product'])
            ->find($id);
    }

    /**
     * Find an order log by UUID (admin).
     */
    public function findByUuid(string $uuid): ?OrderLog
    {
        return OrderLog::withTrashed()
            ->with(['whatsappNumber', 'items.product'])
            ->where('uuid', $uuid)
            ->first();
    }

    /**
     * Soft-delete an order log (admin).
     */
    public function delete(OrderLog $orderLog): void
    {
        $orderLog->delete();
    }

    /**
     * Generate a unique reference code: GB-YYYYMM-NNNNN
     */
    private function generateReferenceCode(): string
    {
        $prefix = 'GB-' . now()->format('Ym') . '-';

        $lastOrder = OrderLog::withTrashed()
            ->where('reference_code', 'LIKE', $prefix . '%')
            ->orderBy('reference_code', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->reference_code, -5);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad((string) $nextNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Build WhatsApp checkout message from template.
     */
    private function buildCheckoutMessage(
        OrderLog $order,
        array $items,
        string $customerName
    ): string {
        $template = SiteSetting::where('setting_key', 'whatsapp_order_template')
            ->value('setting_value');

        if (!$template) {
            // فولباك لو الـ template مش موجود
            $template = "طلب جديد #{reference_code}\nالإجمالي: {total_price}";
        }

        $itemsText = '';
        foreach ($items as $index => $item) {
            $num = $index + 1;
            $itemsText .= "{$num}. {$item['product_name_snapshot']} × {$item['quantity']} = {$item['line_total']} جنيه\n";
        }

        return str_replace(
            ['{customer_name}', '{items}', '{reference_code}', '{total_price}'],
            [$customerName, trim($itemsText), $order->reference_code, $order->total_price],
            $template
        );
    }

    /**
     * Build the WhatsApp deep link URL.
     */
    private function buildWhatsAppUrl(string $phoneNumber, string $message): string
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);
        $encodedMessage = urlencode($message);
        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
    }
}
