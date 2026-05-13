<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BookingStatusLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'changed_by_type',
        'changed_by_id',
        'from_status',
        'to_status',
        'notes',
        'location_lat',
        'location_lng',
    ];

    protected function casts(): array
    {
        return [
            'location_lat' => 'decimal:7',
            'location_lng' => 'decimal:7',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function changedBy(): MorphTo
    {
        return $this->morphTo('changed_by');
    }
}
