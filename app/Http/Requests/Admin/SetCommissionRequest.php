<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SetCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_category_id' => ['nullable', 'integer', 'exists:service_categories,id'],
            'rate' => ['required', 'numeric', 'between:0,100'],
        ];
    }
}
