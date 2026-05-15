<?php

namespace App\Listeners;

use App\Events\NoProvidersAvailable;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class HandleNoProviders
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(NoProvidersAvailable $event): void
    {
        $booking = $event->booking->load('customer');

        Log::warning("No providers available for booking #{$event->booking->reference_number}");

        if (!$booking->customer) return;

        $locale = $booking->customer->language ?? 'en';

        $title = __('notifications.no_providers_title', [], $locale);
        $body = __('notifications.no_providers_body', [], $locale);

        try {
            $this->notificationService->send($booking->customer, 'no_providers', $title, $body, [
                'type' => 'no_providers',
                'booking_ref' => $booking->reference_number,
            ]);
        } catch (\Throwable $e) {
            Log::debug("Failed to notify customer of no providers: {$e->getMessage()}");
        }
    }
}
