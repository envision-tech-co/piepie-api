<?php

namespace App\Http\Controllers\Api\Provider;

use App\Enums\BookingStatus;
use App\Enums\OfferStatus;
use App\Events\NoProvidersAvailable;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Provider\UpdateBookingStatusRequest;
use App\Http\Resources\BookingResource;
use App\Http\Resources\ProviderJobOfferResource;
use App\Models\Booking;
use App\Models\ProviderJobOffer;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * List pending job offers for this provider.
     */
    public function offers(Request $request): JsonResponse
    {
        $provider = $request->user();

        $offers = ProviderJobOffer::with(['booking.serviceCategory', 'booking.customer'])
            ->where('provider_id', $provider->id)
            ->where('status', OfferStatus::Pending)
            ->where('expires_at', '>', now())
            ->orderByDesc('offered_at')
            ->get();

        return response()->json([
            'success' => true,
            'offers' => ProviderJobOfferResource::collection($offers),
        ]);
    }

    /**
     * Accept a job offer.
     */
    public function acceptOffer(Request $request, int $offerId): JsonResponse
    {
        $provider = $request->user();

        $offer = ProviderJobOffer::where('id', $offerId)
            ->where('provider_id', $provider->id)
            ->firstOrFail();

        if ($offer->status !== OfferStatus::Pending) {
            return response()->json([
                'success' => false,
                'message' => 'This offer has already been responded to.',
            ], 422);
        }

        if ($offer->expires_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'This offer has expired.',
            ], 422);
        }

        $booking = $offer->booking;

        try {
            $booking = $this->bookingService->acceptBooking($provider, $booking);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking accepted successfully.',
            'booking' => new BookingResource($booking),
        ]);
    }

    /**
     * Decline a job offer.
     */
    public function declineOffer(Request $request, int $offerId): JsonResponse
    {
        $provider = $request->user();

        $offer = ProviderJobOffer::where('id', $offerId)
            ->where('provider_id', $provider->id)
            ->where('status', OfferStatus::Pending)
            ->firstOrFail();

        $offer->update([
            'status' => OfferStatus::Declined,
            'responded_at' => now(),
        ]);

        // Check if all offers for this booking are declined/expired
        $booking = $offer->booking;
        $pendingOffers = ProviderJobOffer::where('booking_id', $booking->id)
            ->where('status', OfferStatus::Pending)
            ->where('expires_at', '>', now())
            ->count();

        if ($pendingOffers === 0) {
            // Try re-dispatch or fire no providers event
            $this->bookingService->dispatchToNearbyProviders($booking);

            // Check if new offers were created
            $newOffers = ProviderJobOffer::where('booking_id', $booking->id)
                ->where('status', OfferStatus::Pending)
                ->where('expires_at', '>', now())
                ->count();

            if ($newOffers === 0) {
                event(new NoProvidersAvailable($booking));
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Offer declined.',
        ]);
    }

    /**
     * Get current active booking for provider.
     */
    public function active(Request $request): JsonResponse
    {
        $provider = $request->user();

        $booking = Booking::with(['customer', 'serviceCategory', 'statusLogs'])
            ->forProvider($provider->id)
            ->active()
            ->first();

        return response()->json([
            'success' => true,
            'booking' => $booking ? new BookingResource($booking) : null,
        ]);
    }

    /**
     * Update job status (provider side).
     */
    public function updateStatus(UpdateBookingStatusRequest $request, string $reference): JsonResponse
    {
        $provider = $request->user();

        $booking = Booking::with(['customer', 'serviceCategory'])
            ->forProvider($provider->id)
            ->where('reference_number', $reference)
            ->firstOrFail();

        $newStatus = BookingStatus::from($request->input('status'));

        try {
            $booking = $this->bookingService->updateStatus($booking, $newStatus, [
                'actor' => $provider,
                'lat' => $request->input('lat'),
                'lng' => $request->input('lng'),
                'notes' => $request->input('notes'),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated.',
            'booking' => new BookingResource($booking),
        ]);
    }

    /**
     * Provider job history.
     */
    public function index(Request $request): JsonResponse
    {
        $provider = $request->user();

        $query = Booking::with(['customer', 'serviceCategory', 'statusLogs'])
            ->forProvider($provider->id)
            ->orderByDesc('created_at');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        $bookings = $query->paginate(15);

        return response()->json([
            'success' => true,
            'bookings' => BookingResource::collection($bookings),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }
}
