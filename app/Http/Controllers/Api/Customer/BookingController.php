<?php

namespace App\Http\Controllers\Api\Customer;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Customer\CreateBookingRequest;
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
     * Create a new booking.
     */
    public function store(CreateBookingRequest $request): JsonResponse
    {
        $customer = $request->user();

        $booking = $this->bookingService->createBooking($customer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully.',
            'booking' => new BookingResource($booking),
        ], 201);
    }

    /**
     * List customer bookings.
     */
    public function index(Request $request): JsonResponse
    {
        $customer = $request->user();

        $query = Booking::with(['serviceCategory', 'provider', 'statusLogs'])
            ->forCustomer($customer->id)
            ->orderByDesc('created_at');

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
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

    /**
     * Get booking detail by reference number.
     */
    public function show(Request $request, string $reference): JsonResponse
    {
        $customer = $request->user();

        $booking = Booking::with(['serviceCategory', 'provider', 'statusLogs'])
            ->forCustomer($customer->id)
            ->where('reference_number', $reference)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'booking' => new BookingResource($booking),
        ]);
    }

    /**
     * Cancel a booking.
     */
    public function cancel(Request $request, string $reference): JsonResponse
    {
        $customer = $request->user();

        $booking = Booking::forCustomer($customer->id)
            ->where('reference_number', $reference)
            ->firstOrFail();

        if ($booking->status->isTerminal()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking is already in a terminal state.',
            ], 422);
        }

        if (in_array($booking->status, [BookingStatus::InProgress, BookingStatus::Completed])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a booking that is in progress or completed.',
            ], 422);
        }

        $reason = $request->input('reason');

        $booking = $this->bookingService->cancelBooking($booking, 'customer', $reason, $customer);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.',
            'booking' => new BookingResource($booking),
        ]);
    }
}
