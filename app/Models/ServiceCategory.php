<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_en',
        'name_ar',
        'name_ku',
        'description_en',
        'description_ar',
        'description_ku',
        'icon',
        'base_price',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'base_price' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Get the localized name based on current app locale.
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $field = "name_{$locale}";

        return $this->attributes[$field] ?? $this->attributes['name_en'];
    }

    /**
     * Get the localized description based on current app locale.
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $field = "description_{$locale}";

        return $this->attributes[$field] ?? $this->attributes['description_en'];
    }

    /**
     * Scope: active categories ordered by sort_order.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
