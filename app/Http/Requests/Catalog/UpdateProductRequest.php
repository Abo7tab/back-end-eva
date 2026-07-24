<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'brand_uuid'       => 'sometimes|required|uuid|exists:brands,uuid',
            'category_uuid'    => 'sometimes|required|uuid|exists:categories,uuid',
            'name'             => 'sometimes|string|max:255',
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                \Illuminate\Validation\Rule::unique('products', 'slug')
                    ->ignore($this->route('uuid'), 'uuid')
                    ->whereNull('deleted_at'),
            ],
            'sku' => [
                'nullable',
                'string',
                'max:100',
                \Illuminate\Validation\Rule::unique('products', 'sku')
                    ->ignore($this->route('uuid'), 'uuid')
                    ->whereNull('deleted_at'),
            ],
            'short_description' => 'nullable|string',
            'description'      => 'nullable|string',
            'weight'           => 'nullable|numeric|min:0',
            'weight_unit'      => 'nullable|string|in:ml,g,kg,piece',
            'price'            => 'sometimes|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock_quantity'   => 'sometimes|integer|min:0',
            'is_trending'      => 'sometimes|boolean',
            'is_featured'      => 'sometimes|boolean',
            'active_status'    => 'sometimes|boolean',
            'sort_order'       => 'sometimes|integer|min:0',
            'seo_title'        => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:320',
        ];
    }

    public function passedValidation(): void
    {
        if ($this->has('brand_uuid')) {
            $brandId = \App\Domains\Catalog\Models\Brand::where('uuid', $this->input('brand_uuid'))->value('id');
            $this->merge(['brand_id' => $brandId]);
        }
        if ($this->has('category_uuid')) {
            $categoryId = \App\Domains\Catalog\Models\Category::where('uuid', $this->input('category_uuid'))->value('id');
            $this->merge(['category_id' => $categoryId]);
        }
    }

    public function toServiceData(): array
    {
        $data = $this->validated();
        
        if ($this->has('brand_id')) {
            $data['brand_id'] = $this->input('brand_id');
        }
        if ($this->has('category_id')) {
            $data['category_id'] = $this->input('category_id');
        }
        
        unset($data['brand_uuid'], $data['category_uuid']);
        
        return $data;
    }
}
