<?php

declare(strict_types=1);

namespace App\Domains\OrderLogging\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderLogItemResource extends JsonResource
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
            'id' => $this->id,
            'order_log_id' => $this->order_log_id,
            'product_id' => $this->product_id,
            'product_name_snapshot' => $this->product_name_snapshot,
            'product_slug_snapshot' => $this->product_slug_snapshot,
            'product_image_snapshot' => $this->product_image_snapshot,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'line_total' => $this->line_total,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
