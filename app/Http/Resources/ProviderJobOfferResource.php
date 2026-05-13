<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProviderJobOfferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'expires_at' => $this->expires_at?->toISOString(),
            'booking' => new BookingResource($this->whenLoaded('booking')),
            'distance_km' => $this->when(isset($this->distance_km), fn () => round($this->distance_km, 2)),
            'estimated_earnings' => $this->when(
                $this->relationLoaded('booking') && $this->booking,
                function () {
                    $price = (float) $this->booking->estimated_price;
                    $rate = (float) $this->booking->commission_rate;
                    return round($price - ($price * $rate / 100), 2);
                }
            ),
        ];
    }
}
