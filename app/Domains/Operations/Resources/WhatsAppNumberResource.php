<?php

declare(strict_types=1);

namespace App\Domains\Operations\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsAppNumberResource extends JsonResource
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
            'phone_number' => $this->phone_number,
            'display_name' => $this->display_name,
            'is_active' => $this->is_active,
            'order_count' => $this->order_count,
            'last_assigned_at' => $this->last_assigned_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
