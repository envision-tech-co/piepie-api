<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\CancelledBy;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_number',
        'customer_id',
        'service_category_id',
        'provider_id',
        'status',
        'booking_type',
        'scheduled_at',
        'customer_lat',
        'customer_lng',
        'customer_address',
        'provider_lat',
        'provider_lng',
        'customer_notes',
        'estimated_price',
        'final_price',
        'commission_rate',
        'commission_amount',
        'provider_earning',
        'payment_method',
        'payment_status',
        'cancelled_by',
        'cancellation_reason',
        'accepted_at',
        'arrived_at',
        'started_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'booking_type' => BookingType::class,
            'payment_method' => PaymentMethod::class,
            'payment_status' => PaymentStatus::class,
            'cancelled_by' => CancelledBy::class,
            'scheduled_at' => 'datetime',
            'accepted_at' => 'datetime',
            'arrived_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'customer_lat' => 'decimal:7',
            'customer_lng' => 'decimal:7',
            'provider_lat' => 'decimal:7',
            'provider_lng' => 'decimal:7',
            'estimated_price' => 'decimal:2',
            'final_price' => 'decimal:2',
            'commission_rate' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'provider_earning' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Booking $booking) {
            if (empty($booking->reference_number)) {
                $booking->reference_number = static::generateReferenceNumber();
            }
        });
    }

    /**
     * Generate a unique reference number: PP-YYYY-XXXXXX
     */
    public static function generateReferenceNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $prefix = "PP-{$year}-";

            $lastBooking = static::where('reference_number', 'like', "{$prefix}%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            if ($lastBooking) {
                $lastNumber = (int) substr($lastBooking->reference_number, -6);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        });
    }

    // Relationships

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    public function serviceCategory(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function statusLogs(): HasMany
    {
        return $this->hasMany(BookingStatusLog::class)->orderBy('created_at');
    }

    public function jobOffers(): HasMany
    {
        return $this->hasMany(ProviderJobOffer::class);
    }

    // Scopes

    public function scopeForCustomer(Builder $query, int $customerId): Builder
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeForProvider(Builder $query, int $providerId): Builder
    {
        return $query->where('provider_id', $providerId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            BookingStatus::Completed->value,
            BookingStatus::Cancelled->value,
        ]);
    }
}
