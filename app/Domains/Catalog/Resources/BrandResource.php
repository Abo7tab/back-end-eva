<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'logo_url' => $this->logo_url,
            'storage_public_id' => $this->when($request->is('api/*/admin/*'), $this->storage_public_id),
            'seo_title' => $this->seo_title,
            'meta_description' => $this->meta_description,
            'active_status' => $this->active_status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products_count' => $this->whenCounted('products'),
        ];
    }
}
