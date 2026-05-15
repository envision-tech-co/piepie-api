<?php

namespace App\Events;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Booking $booking,
        public BookingStatus $previousStatus
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel("booking.{$this->booking->reference_number}")];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        $data = [
            'status' => $this->booking->status->value,
            'status_label' => $this->booking->status->label(),
            'previous_status' => $this->previousStatus->value,
            'timestamp' => now()->toISOString(),
        ];

        if ($this->booking->provider_id && $this->booking->relationLoaded('provider')) {
            $data['provider'] = [
                'id' => $this->booking->provider->id,
                'name' => $this->booking->provider->name,
            ];
        }

        return $data;
    }
}
