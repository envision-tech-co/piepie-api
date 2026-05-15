<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\LocationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrackingController extends Controller
{
    public function __construct(
        protected LocationService $locationService
    ) {}

    /**
     * Full tracking data for a booking.
     */
    public function show(Request $request, string $reference): JsonResponse
    {
        $customer = $request->user();

        $booking = Booking::with(['provider', 'statusLogs'])
            ->forCustomer($customer->id)
            ->where('reference_number', $reference)
            ->firstOrFail();

        // Provider info
        $providerData = null;
        if ($booking->provider) {
            $providerData = [
                'id' => $booking->provider->id,
                'name' => $booking->provider->name,
                'phone' => $booking->provider->phone,
                'rating' => (float) $booking->provider->overall_rating,
                'profile_photo_url' => $booking->provider->profile_photo
                    ? Storage::url($booking->provider->profile_photo)
                    : null,
                'vehicle_type' => $booking->provider->vehicle_type,
            ];
        }

        // Provider location (privacy-enforced)
        $providerLocation = $this->locationService->getBookingProviderLocation($booking);

        // Status log
        $statusLog = $booking->statusLogs->map(function ($log) {
            return [
                'status' => $log->to_status,
                'label' => ucwords(str_replace('_', ' ', $log->to_status)),
                'time' => $log->created_at->format('H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'booking_ref' => $booking->reference_number,
                'status' => $booking->status->value,
                'status_label' => $booking->status->label(),
                'provider' => $providerData,
                'provider_location' => $providerLocation,
                'customer_location' => [
                    'lat' => (float) $booking->customer_lat,
                    'lng' => (float) $booking->customer_lng,
                ],
                'status_log' => $statusLog,
                'broadcast_channel' => "private-booking.{$booking->reference_number}",
                'broadcast_auth_endpoint' => '/broadcasting/auth',
            ],
        ]);
    }
}
