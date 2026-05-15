<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class ServiceProvider extends Authenticatable
{
    use HasFactory, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'profile_photo',
        'vehicle_type',
        'service_speciality',
        'id_document_path',
        'language',
        'status',
        'is_online',
        'current_lat',
        'current_lng',
        'overall_rating',
        'total_jobs',
        'phone_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'current_lat' => 'decimal:7',
            'current_lng' => 'decimal:7',
            'overall_rating' => 'decimal:2',
            'total_jobs' => 'integer',
            'phone_verified_at' => 'datetime',
        ];
    }

    public function bookings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\Booking::class, 'provider_id');
    }

    public function jobOffers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\ProviderJobOffer::class, 'provider_id');
    }

    public function location(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\ProviderLocation::class, 'provider_id');
    }

    public function deviceTokens(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\DeviceToken::class, 'tokenable');
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(\App\Models\AppNotification::class, 'notifiable');
    }
}
