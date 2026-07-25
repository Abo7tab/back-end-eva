<?php

declare(strict_types=1);

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWhatsAppNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $whatsappNumber = $this->route('whatsapp_number') 
            ?? \App\Domains\Operations\Models\WhatsAppNumber::where('uuid', $this->route('uuid'))->first();
        
        return [
            'phone_number' => [
                'sometimes',
                'string',
                'max:32',
                Rule::unique('whatsapp_numbers', 'phone_number')->ignore($whatsappNumber?->id)
            ],
            'display_name' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
