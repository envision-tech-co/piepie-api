<?php

namespace App\Listeners;

use App\Events\BookingDispatched;
use Illuminate\Support\Facades\Log;

class NotifyProvidersOfNewJob
{
    public function handle(BookingDispatched $event): void
    {
        foreach ($event->providerIds as $providerId) {
            Log::info("Notify provider #{$providerId} of booking #{$event->booking->reference_number}");
        }
    }
}
