<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingStatusLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'from_status' => $this->from_status,
            'to_status' => $this->to_status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
