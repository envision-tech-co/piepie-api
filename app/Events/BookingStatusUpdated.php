<?php

namespace App\Events;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Booking $booking,
        public BookingStatus $previousStatus
    ) {}
}
