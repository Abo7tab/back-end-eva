<?php

declare(strict_types=1);

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class ReorderProductImagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'min:1'],
            'images.*.uuid' => ['required', 'string', 'exists:product_images,uuid'],
            'images.*.sort_order' => ['required', 'integer', 'min:0', 'max:32767'],
        ];
    }
}
