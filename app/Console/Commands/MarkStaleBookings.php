<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Enums\OfferStatus;
use App\Events\NoProvidersAvailable;
use App\Models\Booking;
use App\Models\ProviderJobOffer;
use Illuminate\Console\Command;

class MarkStaleBookings extends Command
{
    protected $signature = 'pippip:mark-stale-bookings';
    protected $description = 'Find bookings stuck in searching status with no active offers';

    public function handle(): int
    {
        $staleBookings = Booking::where('status', BookingStatus::Searching->value)
            ->where('created_at', '<', now()->subMinutes(10))
            ->get();

        $count = 0;

        foreach ($staleBookings as $booking) {
            // Check if there are any active (pending + not expired) offers
            $activeOffers = ProviderJobOffer::where('booking_id', $booking->id)
                ->where('status', OfferStatus::Pending->value)
                ->where('expires_at', '>', now())
                ->count();

            if ($activeOffers === 0) {
                event(new NoProvidersAvailable($booking));
                $count++;
            }
        }

        if ($count > 0) {
            $this->info("Fired NoProvidersAvailable for {$count} stale bookings.");
        }

        return self::SUCCESS;
    }
}
