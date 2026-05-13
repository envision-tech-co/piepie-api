<?php

namespace App\Listeners;

use App\Events\BookingAccepted;
use Illuminate\Support\Facades\Log;

class NotifyCustomerOfAcceptance
{
    public function handle(BookingAccepted $event): void
    {
        Log::info("Notify customer #{$event->booking->customer_id} of acceptance for booking #{$event->booking->reference_number}");
    }
}
