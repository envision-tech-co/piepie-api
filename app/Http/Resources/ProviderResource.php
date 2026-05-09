<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'vehicle_type' => $this->vehicle_type,
            'service_speciality' => $this->service_speciality,
            'status' => $this->status,
            'is_online' => $this->is_online,
            'overall_rating' => (float) $this->overall_rating,
            'total_jobs' => $this->total_jobs,
            'profile_photo_url' => $this->profile_photo 
                ? Storage::url($this->profile_photo) 
                : null,
            'language' => $this->language,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
