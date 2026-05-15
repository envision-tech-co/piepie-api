<?php

namespace App\Listeners;

use App\Events\BookingAccepted;
use App\Services\LocationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NotifyCustomerOfAcceptance
{
    public function __construct(
        protected NotificationService $notificationService,
        protected LocationService $locationService
    ) {}

    public function handle(BookingAccepted $event): void
    {
        $booking = $event->booking->load(['customer', 'provider']);

        if (!$booking->customer) return;

        $locale = $booking->customer->language ?? 'en';
        $providerName = $booking->provider->name ?? 'Provider';

        // Calculate ETA
        $eta = '—';
        $providerLocation = $this->locationService->getProviderLocation($booking->provider_id);
        if ($providerLocation) {
            $etaMinutes = $this->locationService->calculateETA(
                $providerLocation->lat,
                $providerLocation->lng,
                (float) $booking->customer_lat,
                (float) $booking->customer_lng
            );
            $eta = (string) $etaMinutes;
        }

        $title = __('notifications.accepted_title', [], $locale);
        $body = __('notifications.accepted_body', [
            'provider_name' => $providerName,
            'eta' => $eta,
        ], $locale);

        $data = [
            'type' => 'booking_accepted',
            'booking_ref' => $booking->reference_number,
            'provider_id' => (string) $booking->provider_id,
        ];

        try {
            $this->notificationService->send($booking->customer, 'booking_accepted', $title, $body, $data);
        } catch (\Throwable $e) {
            Log::debug("Failed to notify customer of acceptance: {$e->getMessage()}");
        }
    }
}
