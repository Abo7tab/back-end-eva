<?php

declare(strict_types=1);

namespace App\Http\Requests\Operations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'settings' => ['required', 'array'],
            'settings.*.setting_key' => ['required', 'string', 'exists:site_settings,setting_key'],
            'settings.*.setting_value' => ['nullable', 'string'],
        ];
    }
}
