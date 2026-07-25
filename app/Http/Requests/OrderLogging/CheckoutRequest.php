<?php

declare(strict_types=1);

namespace App\Http\Requests\OrderLogging;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:32'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_uuid' => ['required', 'uuid', 'exists:products,uuid'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
