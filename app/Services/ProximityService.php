<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\ServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProximityService
{
    /**
     * Find nearby providers using Haversine formula in SQL.
     *
     * @param float $lat Customer latitude
     * @param float $lng Customer longitude
     * @param float $radiusKm Search radius in kilometers
     * @param int $limit Maximum number of providers to return
     * @return Collection
     */
    public function findNearbyProviders(float $lat, float $lng, float $radiusKm = 10, int $limit = 5): Collection
    {
        $terminalStatuses = [BookingStatus::Completed->value, BookingStatus::Cancelled->value];

        // Use a subquery approach to avoid HAVING clause issues with MySQL strict mode
        $haversine = "(6371 * acos(LEAST(1.0, cos(radians({$lat})) * cos(radians(current_lat)) * cos(radians(current_lng) - radians({$lng})) + sin(radians({$lat})) * sin(radians(current_lat)))))";

        $subQuery = DB::table('service_providers')
            ->select('*')
            ->selectRaw("{$haversine} AS distance_km")
            ->where('is_online', true)
            ->where('status', 'approved')
            ->whereNotNull('current_lat')
            ->whereNotNull('current_lng')
            ->whereNotExists(function ($query) use ($terminalStatuses) {
                $query->select(DB::raw(1))
                    ->from('bookings')
                    ->whereColumn('bookings.provider_id', 'service_providers.id')
                    ->whereNotIn('bookings.status', $terminalStatuses);
            });

        // Wrap in outer query to filter by distance
        $results = DB::table(DB::raw("({$subQuery->toSql()}) as providers"))
            ->mergeBindings($subQuery)
            ->where('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km')
            ->limit($limit)
            ->get();

        // Hydrate into ServiceProvider models
        return $results->map(function ($row) {
            $provider = new ServiceProvider((array) $row);
            $provider->exists = true;
            $provider->id = $row->id;
            $provider->distance_km = $row->distance_km;
            return $provider;
        });
    }
}
