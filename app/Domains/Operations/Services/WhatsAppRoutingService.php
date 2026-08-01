<?php

declare(strict_types=1);

namespace App\Domains\Operations\Services;

use App\Domains\Operations\Models\WhatsAppNumber;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class WhatsAppRoutingService
{
    /**
     * Get the next WhatsApp number using round-robin routing.
     *
     * Race-condition safe per §6.4:
     * 1. DB::transaction wrapping the whole operation
     * 2. lockForUpdate on the candidate row
     * 3. Select active number with oldest last_assigned_at
     * 4. Atomically increment order_count and update last_assigned_at
     * 5. If no active number, return null
     */
    public function assignNextNumber(): ?WhatsAppNumber
    {
        return DB::transaction(function () {
            $number = WhatsAppNumber::active()
                ->orderBy('last_assigned_at', 'asc')
                ->orderBy('order_count', 'asc')
                ->orderBy('id', 'asc')
                ->lockForUpdate()
                ->first();

            if ($number === null) {
                return null;
            }

            $number->increment('order_count');
            $number->update(['last_assigned_at' => now()]);

            return $number->fresh();
        });
    }

    /**
     * List all WhatsApp numbers (admin, paginated).
     */
    public function listForAdmin(int $perPage = 12): LengthAwarePaginator
    {
        return WhatsAppNumber::orderBy('is_active', 'desc')
            ->orderBy('order_count', 'asc')
            ->paginate($perPage);
    }

    /**
     * Find a WhatsApp number by ID.
     */
    public function findById(int $id): ?WhatsAppNumber
    {
        return WhatsAppNumber::find($id);
    }

    /**
     * Find a WhatsApp number by UUID.
     */
    public function findByUuid(string $uuid): ?WhatsAppNumber
    {
        return WhatsAppNumber::where('uuid', $uuid)->first();
    }

    /**
     * Create a new WhatsApp number.
     */
    public function create(array $data): WhatsAppNumber
    {
        return WhatsAppNumber::create($data);
    }

    /**
     * Update an existing WhatsApp number.
     */
    public function update(WhatsAppNumber $number, array $data): WhatsAppNumber
    {
        $number->update($data);
        return $number->fresh();
    }

    /**
     * Delete a WhatsApp number.
     */
    public function delete(WhatsAppNumber $number): void
    {
        $number->delete();
    }
}
