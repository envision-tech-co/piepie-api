<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderLocation extends Model
{
    protected $fillable = [
        'provider_id',
        'lat',
        'lng',
        'heading',
        'speed_kmh',
        'accuracy_meters',
        'booking_id',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'heading' => 'float',
            'speed_kmh' => 'float',
            'accuracy_meters' => 'float',
        ];
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
