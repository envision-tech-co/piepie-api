<?php

namespace App\Listeners;

use App\Events\BookingStatusUpdated;
use Illuminate\Support\Facades\Log;

class NotifyCustomerOfStatusUpdate
{
    public function handle(BookingStatusUpdated $event): void
    {
        Log::info("Booking #{$event->booking->reference_number} status updated from {$event->previousStatus->value} to {$event->booking->status->value}");
    }
}
