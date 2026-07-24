<?php

declare(strict_types=1);

namespace App\Domains\OrderLogging\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Domains\Operations\Resources\WhatsAppNumberResource;

class OrderLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'reference_code' => $this->reference_code,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'total_items' => $this->total_items,
            'subtotal' => $this->subtotal,
            'total_price' => $this->total_price,
            'whatsapp_number_id' => $this->whatsapp_number_id,
            'assigned_phone_number' => $this->assigned_phone_number,
            'checkout_message' => $this->checkout_message,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'whatsapp_number' => WhatsAppNumberResource::make($this->whenLoaded('whatsappNumber')),
            'items' => OrderLogItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
