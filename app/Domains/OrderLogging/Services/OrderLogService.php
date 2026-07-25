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
     * List all orders (admin, paginated, searchable).
     */
    public function listForAdmin(int $perPage = 20, ?string $search = null, ?string $status = null, ?string $sortBy = null): LengthAwarePaginator
    {
        $query = OrderLog::withTrashed()->with(['items', 'whatsappNumber']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'LIKE', "%{$search}%")
                  ->orWhere('customer_name', 'LIKE', "%{$search}%")
                  ->orWhere('customer_phone', 'LIKE', "%{$search}%");
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest_price':
                $query->orderBy('total_price', 'desc');
                break;
            case 'lowest_price':
                $query->orderBy('total_price', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get order stats for dashboard/admin.
     */
    public function getStats(): array
    {
        return [
            'pending' => OrderLog::where('status', 'pending')->count(),
            'redirected' => OrderLog::where('status', 'redirected')->count(),
            'completed' => OrderLog::where('status', 'completed')->count(),
            'failed' => OrderLog::where('status', 'failed')->count(),
        ];
    }

    /**
     * Update order status (admin).
     */
    public function updateStatus(string $uuid, string $status): OrderLog
    {
        $order = OrderLog::where('uuid', $uuid)->firstOrFail();
        
        $validStatuses = ['pending', 'redirected', 'completed', 'failed'];
        if (!in_array($status, $validStatuses, true)) {
            abort(422, 'حالة الطلب غير صالحة');
        }
        
        $order->update(['status' => $status]);
        return $order->fresh();
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

        $includeImagesStr = SiteSetting::where('setting_key', 'whatsapp_include_images')
            ->value('setting_value');
        $includeImages = filter_var($includeImagesStr ?? 'true', FILTER_VALIDATE_BOOLEAN);

        $itemsText = '';
        foreach ($items as $index => $item) {
            $num = $index + 1;
            $itemsText .= "{$num}. {$item['product_name_snapshot']} × {$item['quantity']} = {$item['line_total']} جنيه\n";
            if ($includeImages && !empty($item['product_image_snapshot'])) {
                $itemsText .= "   {$item['product_image_snapshot']}\n";
            }
        }

        return str_replace(
            [
                '{customer_name}', 
                '{customer_phone}',
                '{items}', 
                '{reference_code}', 
                '{subtotal}',
                '{total_price}',
                '{total_items}'
            ],
            [
                $customerName, 
                $order->customer_phone ?? '',
                trim($itemsText), 
                $order->reference_code, 
                $order->subtotal,
                $order->total_price,
                $order->total_items
            ],
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
