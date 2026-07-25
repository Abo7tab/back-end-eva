<?php declare(strict_types=1);

namespace App\Domains\Catalog\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid'              => $this->uuid,
            'name'              => $this->name,
            'slug'              => $this->slug,
            'sku'               => $this->sku,
            'short_description' => $this->short_description,
            'description'       => $this->description,
            'weight'            => $this->weight,
            'weight_unit'       => $this->weight_unit,
            'price'             => $this->price,
            'compare_at_price'  => $this->compare_at_price,
            'stock_quantity'    => $this->stock_quantity,
            'cover_image_url'   => $this->cover_image_url,
            'is_trending'       => (bool) $this->is_trending,
            'is_featured'       => (bool) $this->is_featured,
            'views_count'       => $this->views_count,
            'sort_order'        => $this->sort_order,
            'active_status'     => (bool) $this->active_status,
            'seo_title'         => $this->seo_title,
            'meta_description'  => $this->meta_description,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
            
            // Relationships
            'brand'    => $this->whenLoaded('brand', fn() => [
                'uuid' => $this->brand->uuid,
                'name' => $this->brand->name,
                'slug' => $this->brand->slug,
            ]),
            'category' => $this->whenLoaded('category', fn() => [
                'uuid' => $this->category->uuid,
                'name' => $this->category->name,
                'slug' => $this->category->slug,
            ]),
            'images'   => $this->whenLoaded('images', fn() => 
                $this->images->map(fn($img) => [
                    'uuid'       => $img->uuid,
                    'image_url'  => $img->image_url,
                    'alt_text'   => $img->alt_text,
                    'sort_order' => $img->sort_order,
                ])
            ),
        ];
    }
}
