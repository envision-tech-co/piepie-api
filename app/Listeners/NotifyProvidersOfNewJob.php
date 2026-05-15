<?php

namespace App\Listeners;

use App\Events\BookingDispatched;
use App\Models\ProviderJobOffer;
use App\Models\ServiceProvider;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NotifyProvidersOfNewJob
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(BookingDispatched $event): void
    {
        $booking = $event->booking->load('serviceCategory');
        $currency = config('pippip.currency', 'IQD');

        foreach ($event->providerIds as $providerId) {
            $provider = ServiceProvider::find($providerId);
            if (!$provider) continue;

            $locale = $provider->language ?? 'en';

            // Get the offer for this provider
            $offer = ProviderJobOffer::where('booking_id', $booking->id)
                ->where('provider_id', $providerId)
                ->first();

            $serviceName = $booking->serviceCategory->name_en ?? 'Service';
            $estimatedEarning = (float) $booking->estimated_price * (1 - (float) $booking->commission_rate / 100);

            $title = __('notifications.new_job_title', [], $locale);
            $body = __('notifications.new_job_body', [
                'service' => $serviceName,
                'distance' => '~',
                'amount' => number_format($estimatedEarning),
                'currency' => $currency,
            ], $locale);

            $data = [
                'type' => 'new_job_offer',
                'booking_ref' => $booking->reference_number,
                'offer_id' => (string) ($offer?->id ?? ''),
                'expires_at' => $offer?->expires_at?->toISOString() ?? '',
            ];

            try {
                $this->notificationService->send($provider, 'new_job_offer', $title, $body, $data);
            } catch (\Throwable $e) {
                Log::debug("Failed to notify provider #{$providerId}: {$e->getMessage()}");
            }
        }

        Log::info("Dispatched booking #{$booking->reference_number} to " . count($event->providerIds) . " providers");
    }
}
