<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ProviderLocation;
use App\Models\ServiceProvider;
use Illuminate\Http\JsonResponse;

class LiveMonitorController extends Controller
{
    /**
     * All active bookings with provider locations.
     */
    public function bookings(): JsonResponse
    {
        $bookings = Booking::with(['customer', 'provider', 'serviceCategory'])
            ->active()
            ->orderByDesc('created_at')
            ->get();

        // Get all provider locations in one query
        $providerIds = $bookings->pluck('provider_id')->filter()->unique()->toArray();
        $locations = ProviderLocation::whereIn('provider_id', $providerIds)
            ->get()
            ->keyBy('provider_id');

        $data = $bookings->map(function ($booking) use ($locations) {
            $providerLocation = $booking->provider_id ? ($locations[$booking->provider_id] ?? null) : null;

            return [
                'reference_number' => $booking->reference_number,
                'status' => $booking->status->value,
                'status_label' => $booking->status->label(),
                'service' => $booking->serviceCategory->name_en ?? null,
                'customer' => $booking->customer ? [
                    'name' => $booking->customer->name,
                    'phone' => $booking->customer->phone,
                    'lat' => (float) $booking->customer_lat,
                    'lng' => (float) $booking->customer_lng,
                ] : null,
                'provider' => $booking->provider ? [
                    'name' => $booking->provider->name,
                    'phone' => $booking->provider->phone,
                    'lat' => $providerLocation?->lat,
                    'lng' => $providerLocation?->lng,
                    'last_seen' => $providerLocation?->updated_at?->toISOString(),
                ] : null,
                'created_at' => $booking->created_at->toISOString(),
                'minutes_ago' => $booking->created_at->diffInMinutes(now()),
            ];
        });

        return response()->json([
            'success' => true,
            'bookings' => $data,
        ]);
    }

    /**
     * All online providers with locations.
     */
    public function providers(): JsonResponse
    {
        $providers = ServiceProvider::where('is_online', true)
            ->where('status', 'approved')
            ->get();

        $providerIds = $providers->pluck('id')->toArray();
        $locations = ProviderLocation::whereIn('provider_id', $providerIds)
            ->get()
            ->keyBy('provider_id');

        // Get active bookings for these providers
        $activeBookings = Booking::active()
            ->whereIn('provider_id', $providerIds)
            ->get()
            ->keyBy('provider_id');

        $data = $providers->map(function ($provider) use ($locations, $activeBookings) {
            $location = $locations[$provider->id] ?? null;
            $activeBooking = $activeBookings[$provider->id] ?? null;

            return [
                'id' => $provider->id,
                'name' => $provider->name,
                'phone' => $provider->phone,
                'speciality' => $provider->service_speciality,
                'rating' => (float) $provider->overall_rating,
                'location' => $location ? [
                    'lat' => $location->lat,
                    'lng' => $location->lng,
                    'heading' => $location->heading,
                    'updated_at' => $location->updated_at->toISOString(),
                ] : null,
                'active_booking_ref' => $activeBooking?->reference_number,
            ];
        });

        return response()->json([
            'success' => true,
            'providers' => $data,
        ]);
    }
}
