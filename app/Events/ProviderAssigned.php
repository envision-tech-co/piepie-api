<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public ?int $etaMinutes = null
    ) {}
}
