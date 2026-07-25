<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'parent_uuid' => ['nullable', 'uuid', 'exists:categories,uuid'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'cover_image_url' => ['nullable', 'url', 'max:2048'],
            'storage_public_id' => ['nullable', 'string', 'max:255'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'active_status' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0', 'max:32767'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->filled('parent_uuid')) {
                $parent = \App\Domains\Catalog\Models\Category::where('uuid', $this->input('parent_uuid'))->first();
                if ($parent) {
                    $newDepth = app(\App\Domains\Catalog\Services\CategoryService::class)->getDepth($parent->id);
                    if ($newDepth > 3) {
                        $validator->errors()->add(
                            'parent_uuid', 
                            'لا يمكن إضافة قسم في هذا المستوى. الحد الأقصى 3 مستويات.'
                        );
                    }
                }
            }
        });
    }

    public function passedValidation(): void
    {
        if ($this->filled('parent_uuid')) {
            $parentId = \App\Domains\Catalog\Models\Category::where('uuid', $this->input('parent_uuid'))->value('id');
            $this->merge(['parent_id' => $parentId]);
        } else {
            $this->merge(['parent_id' => null]);
        }
    }

    public function toServiceData(): array
    {
        $data = $this->validated();
        
        if ($this->has('parent_id') || $this->has('parent_uuid')) {
            $data['parent_id'] = $this->input('parent_id');
        }
        
        unset($data['parent_uuid']);
        
        return $data;
    }
}
