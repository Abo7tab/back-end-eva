<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_uuid'       => 'required|uuid|exists:brands,uuid',
            'category_uuid'    => 'required|uuid|exists:categories,uuid',
            'name'             => 'required|string|max:255',
            'sku'              => 'nullable|string|max:100|unique:products,sku',
            'short_description' => 'nullable|string',
            'description'      => 'nullable|string',
            'weight'           => 'nullable|numeric|min:0',
            'weight_unit'      => 'nullable|string|in:ml,g,kg,piece',
            'price'            => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_quantity'   => 'sometimes|integer|min:0',
            'is_trending'      => 'sometimes|boolean',
            'is_featured'      => 'sometimes|boolean',
            'active_status'    => 'sometimes|boolean',
            'cover_image_url'  => 'nullable|url|max:2048',
            'storage_public_id'=> 'nullable|string|max:255',
            'sort_order'       => 'sometimes|integer|min:0',
            'seo_title'        => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
        ];
    }

    public function passedValidation(): void
    {
        $brandId = \App\Domains\Catalog\Models\Brand::where('uuid', $this->input('brand_uuid'))->value('id');
        $categoryId = \App\Domains\Catalog\Models\Category::where('uuid', $this->input('category_uuid'))->value('id');
        
        $this->merge([
            'brand_id'    => $brandId,
            'category_id' => $categoryId,
        ]);
    }

    public function toServiceData(): array
    {
        $data = $this->validated();
        
        $data['brand_id']    = $this->input('brand_id');
        $data['category_id'] = $this->input('category_id');
        
        unset($data['brand_uuid'], $data['category_uuid']);
        
        return $data;
    }
}
