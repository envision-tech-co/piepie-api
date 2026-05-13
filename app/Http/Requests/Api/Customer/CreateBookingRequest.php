<?php

namespace App\Http\Requests\Api\Customer;

use Illuminate\Foundation\Http\FormRequest;

class CreateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_category_id' => ['required', 'integer', 'exists:service_categories,id'],
            'customer_lat' => ['required', 'numeric', 'between:-90,90'],
            'customer_lng' => ['required', 'numeric', 'between:-180,180'],
            'customer_address' => ['required', 'string', 'max:1000'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
            'booking_type' => ['sometimes', 'string', 'in:immediate,scheduled'],
            'scheduled_at' => ['required_if:booking_type,scheduled', 'nullable', 'date', 'after:now'],
            'payment_method' => ['sometimes', 'string', 'in:cash,card,upi,wallet'],
        ];
    }
}
