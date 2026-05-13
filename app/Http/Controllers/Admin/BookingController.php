<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * List all bookings with filters.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with(['customer', 'provider', 'serviceCategory', 'statusLogs']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('service_category_id')) {
            $query->where('service_category_id', $request->input('service_category_id'));
        }

        if ($request->has('provider_id')) {
            $query->where('provider_id', $request->input('provider_id'));
        }

        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->input('customer_id'));
        }

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->orderByDesc('created_at')->paginate(20);

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

    /**
     * Get booking detail.
     */
    public function show(string $reference): JsonResponse
    {
        $booking = Booking::with(['customer', 'provider', 'serviceCategory', 'statusLogs', 'jobOffers.provider'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'booking' => new BookingResource($booking),
        ]);
    }

    /**
     * Live bookings (non-terminal, no pagination).
     */
    public function live(): JsonResponse
    {
        $bookings = Booking::with(['customer', 'provider', 'serviceCategory'])
            ->active()
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'bookings' => BookingResource::collection($bookings),
        ]);
    }

    /**
     * Admin force cancel a booking.
     */
    public function cancel(Request $request, string $reference): JsonResponse
    {
        $admin = $request->user('admin');

        $booking = Booking::where('reference_number', $reference)->firstOrFail();

        if ($booking->status->isTerminal()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already in a terminal state.',
            ], 422);
        }

        $reason = $request->input('reason', 'Cancelled by admin');

        $booking = $this->bookingService->cancelBooking($booking, 'admin', $reason, $admin);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled by admin.',
            'booking' => new BookingResource($booking),
        ]);
    }
}
