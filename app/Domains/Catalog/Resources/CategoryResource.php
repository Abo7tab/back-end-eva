<?php

declare(strict_types=1);

namespace App\Domains\Catalog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'parent_uuid' => $this->whenLoaded('parent', fn() => $this->parent?->uuid, null),
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'cover_image_url' => $this->cover_image_url,
            'storage_public_id' => $this->when($request->is('api/*/admin/*'), $this->storage_public_id),
            'seo_title' => $this->seo_title,
            'meta_description' => $this->meta_description,
            'active_status' => $this->active_status,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'products_count' => $this->whenCounted('products'),
            'parent' => CategoryResource::make($this->whenLoaded('parent')),
            'children' => CategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
