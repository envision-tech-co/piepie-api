<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\ServiceCategory;
use App\Services\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingWebController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    public function index(Request $request): View
    {
        $query = Booking::with(['customer', 'provider', 'serviceCategory']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('service_category_id')) {
            $query->where('service_category_id', $request->input('service_category_id'));
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', fn ($cq) => $cq->where('name', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $categories = ServiceCategory::orderBy('sort_order')->get();

        return view('admin.bookings.index', compact('bookings', 'categories'));
    }

    public function show(string $reference): View
    {
        $booking = Booking::with(['customer', 'provider', 'serviceCategory', 'statusLogs', 'jobOffers.provider'])
            ->where('reference_number', $reference)
            ->firstOrFail();

        return view('admin.bookings.show', compact('booking'));
    }

    public function live(): View
    {
        $bookings = Booking::with(['customer', 'provider', 'serviceCategory'])
            ->active()
            ->orderByDesc('created_at')
            ->get();

        return view('admin.bookings.live', compact('bookings'));
    }

    public function cancel(Request $request, string $reference): RedirectResponse
    {
        $admin = $request->user('admin');

        $booking = Booking::where('reference_number', $reference)->firstOrFail();

        if ($booking->status->isTerminal()) {
            return back()->with('error', 'Booking is already in a terminal state.');
        }

        $reason = $request->input('reason', 'Cancelled by admin');

        try {
            $this->bookingService->cancelBooking($booking, 'admin', $reason, $admin);
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Booking cancelled by admin.');
    }
}
