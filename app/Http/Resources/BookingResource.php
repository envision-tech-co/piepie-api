<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'reference_number' => $this->reference_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'booking_type' => $this->booking_type->value,
            'scheduled_at' => $this->scheduled_at?->toISOString(),
            'service_category' => [
                'id' => $this->serviceCategory?->id,
                'name' => $this->serviceCategory?->name,
                'icon' => $this->serviceCategory?->icon,
                'base_price' => $this->serviceCategory ? (float) $this->serviceCategory->base_price : null,
            ],
            'customer' => $this->when($this->relationLoaded('customer'), function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->name,
                    'phone' => $this->customer->phone,
                    'profile_photo_url' => $this->customer->profile_photo
                        ? \Illuminate\Support\Facades\Storage::url($this->customer->profile_photo)
                        : null,
                ];
            }),
            'provider' => $this->when($this->provider_id && $this->relationLoaded('provider'), function () {
                return new ProviderResource($this->provider);
            }),
            'location' => [
                'lat' => (float) $this->customer_lat,
                'lng' => (float) $this->customer_lng,
                'address' => $this->customer_address,
            ],
            'estimated_price' => (float) $this->estimated_price,
            'final_price' => $this->final_price ? (float) $this->final_price : null,
            'commission_rate' => (float) $this->commission_rate,
            'commission_amount' => $this->commission_amount ? (float) $this->commission_amount : null,
            'provider_earning' => $this->provider_earning ? (float) $this->provider_earning : null,
            'payment_method' => $this->payment_method->value,
            'payment_status' => $this->payment_status->value,
            'customer_notes' => $this->customer_notes,
            'cancelled_by' => $this->cancelled_by?->value,
            'cancellation_reason' => $this->cancellation_reason,
            'accepted_at' => $this->accepted_at?->toISOString(),
            'arrived_at' => $this->arrived_at?->toISOString(),
            'started_at' => $this->started_at?->toISOString(),
            'completed_at' => $this->completed_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'status_log' => $this->when(
                $this->relationLoaded('statusLogs'),
                fn () => BookingStatusLogResource::collection($this->statusLogs)
            ),
        ];
    }
}
