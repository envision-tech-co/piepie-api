<?php

namespace App\Http\Controllers\Api\Provider;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\UpdateLocationRequest;
use App\Models\Booking;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    /**
     * Provider pings their GPS location.
     */
    public function update(UpdateLocationRequest $request): JsonResponse
    {
        $provider = $request->user();

        // Get active booking for this provider (if any)
        $activeBooking = Booking::forProvider($provider->id)->active()->first();

        $location = $this->locationService->updateProviderLocation($provider, [
            'lat' => $request->input('lat'),
            'lng' => $request->input('lng'),
            'heading' => $request->input('heading'),
            'speed_kmh' => $request->input('speed_kmh'),
            'accuracy_meters' => $request->input('accuracy_meters'),
            'booking_id' => $activeBooking?->id,
        ]);

        // Also update the provider's current_lat/lng on the model
        $provider->update([
            'current_lat' => $request->input('lat'),
            'current_lng' => $request->input('lng'),
        ]);

        return response()->json([
            'success' => true,
            'updated_at' => $location->updated_at->toISOString(),
        ]);
    }

    /**
     * Get own current location.
     */
    public function current(Request $request): JsonResponse
    {
        $provider = $request->user();

        $location = $this->locationService->getProviderLocation($provider->id);

        if (!$location) {
            return response()->json([
                'success' => true,
                'location' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'location' => [
                'lat' => $location->lat,
                'lng' => $location->lng,
                'heading' => $location->heading,
                'speed_kmh' => $location->speed_kmh,
                'accuracy_meters' => $location->accuracy_meters,
                'updated_at' => $location->updated_at->toISOString(),
            ],
        ]);
    }
}
