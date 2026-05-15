<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\ProviderLocationUpdated;
use App\Models\Booking;
use App\Models\ProviderLocation;
use App\Models\ServiceProvider;

class LocationService
{
    /**
     * Upsert provider location (high-frequency, must be fast).
     */
    public function updateProviderLocation(ServiceProvider $provider, array $data): ProviderLocation
    {
        $location = ProviderLocation::updateOrCreate(
            ['provider_id' => $provider->id],
            [
                'lat' => $data['lat'],
                'lng' => $data['lng'],
                'heading' => $data['heading'] ?? null,
                'speed_kmh' => $data['speed_kmh'] ?? null,
                'accuracy_meters' => $data['accuracy_meters'] ?? null,
                'booking_id' => $data['booking_id'] ?? null,
            ]
        );

        try {
            event(new ProviderLocationUpdated($provider, $location, $data['booking_id'] ?? null));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug("Broadcasting location failed: {$e->getMessage()}");
        }

        return $location;
    }

    /**
     * Get current provider location.
     */
    public function getProviderLocation(int $providerId): ?ProviderLocation
    {
        return ProviderLocation::where('provider_id', $providerId)->first();
    }

    /**
     * Get provider location for a booking (privacy-enforced).
     * Only returns data when status is on_the_way or arrived.
     */
    public function getBookingProviderLocation(Booking $booking): ?array
    {
        if (!in_array($booking->status, [BookingStatus::OnTheWay, BookingStatus::Arrived])) {
            return null;
        }

        if (!$booking->provider_id) {
            return null;
        }

        $location = ProviderLocation::where('provider_id', $booking->provider_id)->first();

        if (!$location) {
            return null;
        }

        $isStale = $location->updated_at->diffInSeconds(now()) > 30;

        $eta = $this->calculateETA(
            $location->lat,
            $location->lng,
            (float) $booking->customer_lat,
            (float) $booking->customer_lng
        );

        return [
            'lat' => $location->lat,
            'lng' => $location->lng,
            'heading' => $location->heading,
            'speed_kmh' => $location->speed_kmh,
            'eta_minutes' => $eta,
            'updated_at' => $location->updated_at->toISOString(),
            'is_stale' => $isStale,
        ];
    }

    /**
     * Calculate approximate ETA in minutes using straight-line distance.
     * Assumes average speed of 40 km/h.
     */
    public function calculateETA(float $providerLat, float $providerLng, float $destLat, float $destLng): int
    {
        $distanceKm = $this->haversineDistance($providerLat, $providerLng, $destLat, $destLng);
        $minutes = ($distanceKm / 40) * 60;

        return max(1, (int) ceil($minutes));
    }

    /**
     * Haversine distance in km.
     */
    private function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371;

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
