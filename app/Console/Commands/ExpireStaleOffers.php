<?php

namespace App\Console\Commands;

use App\Enums\OfferStatus;
use App\Events\NoProvidersAvailable;
use App\Models\Booking;
use App\Models\ProviderJobOffer;
use Illuminate\Console\Command;

class ExpireStaleOffers extends Command
{
    protected $signature = 'pippip:expire-stale-offers';
    protected $description = 'Expire pending job offers that have passed their expiry time';

    public function handle(): int
    {
        // Find and expire stale offers
        $expiredOffers = ProviderJobOffer::where('status', OfferStatus::Pending->value)
            ->where('expires_at', '<', now())
            ->get();

        if ($expiredOffers->isEmpty()) {
            return self::SUCCESS;
        }

        // Mark them expired in bulk
        ProviderJobOffer::where('status', OfferStatus::Pending->value)
            ->where('expires_at', '<', now())
            ->update(['status' => OfferStatus::Expired->value, 'responded_at' => now()]);

        $this->info("Expired {$expiredOffers->count()} stale offers.");

        // Check if any bookings now have ALL offers expired/declined
        $bookingIds = $expiredOffers->pluck('booking_id')->unique();

        foreach ($bookingIds as $bookingId) {
            $pendingCount = ProviderJobOffer::where('booking_id', $bookingId)
                ->where('status', OfferStatus::Pending->value)
                ->where('expires_at', '>', now())
                ->count();

            if ($pendingCount === 0) {
                $booking = Booking::find($bookingId);
                if ($booking && $booking->status->value === 'searching') {
                    event(new NoProvidersAvailable($booking));
                    $this->info("Fired NoProvidersAvailable for booking #{$booking->reference_number}");
                }
            }
        }

        return self::SUCCESS;
    }
}
