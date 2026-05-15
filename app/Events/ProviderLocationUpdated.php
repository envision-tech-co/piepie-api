<?php

namespace App\Events;

use App\Models\ProviderLocation;
use App\Models\ServiceProvider;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ServiceProvider $provider,
        public ProviderLocation $location,
        public ?int $bookingId = null
    ) {}

    public function broadcastOn(): array
    {
        if ($this->bookingId) {
            return [new PrivateChannel("booking.{$this->bookingId}")];
        }

        return [];
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'lat' => $this->location->lat,
            'lng' => $this->location->lng,
            'heading' => $this->location->heading,
            'speed_kmh' => $this->location->speed_kmh,
            'updated_at' => $this->location->updated_at?->toISOString(),
        ];
    }

    public function broadcastWhen(): bool
    {
        return $this->bookingId !== null;
    }
}
