<?php

namespace App\Models;

use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderJobOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'provider_id',
        'status',
        'offered_at',
        'responded_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OfferStatus::class,
            'offered_at' => 'datetime',
            'responded_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }
}
