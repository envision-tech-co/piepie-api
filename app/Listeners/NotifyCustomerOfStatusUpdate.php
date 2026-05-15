<?php

namespace App\Listeners;

use App\Enums\BookingStatus;
use App\Events\BookingStatusUpdated;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NotifyCustomerOfStatusUpdate
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function handle(BookingStatusUpdated $event): void
    {
        $booking = $event->booking->load(['customer', 'provider']);

        if (!$booking->customer) return;

        $status = $booking->status;
        $locale = $booking->customer->language ?? 'en';
        $currency = config('pippip.currency', 'IQD');

        // Map status to notification keys
        $statusMap = [
            BookingStatus::OnTheWay->value => 'on_the_way',
            BookingStatus::Arrived->value => 'arrived',
            BookingStatus::InProgress->value => 'in_progress',
            BookingStatus::Completed->value => 'completed',
            BookingStatus::Cancelled->value => 'cancelled',
        ];

        $key = $statusMap[$status->value] ?? null;
        if (!$key) return;

        $title = __("notifications.{$key}_title", [], $locale);
        $body = __("notifications.{$key}_body", [], $locale);

        $data = [
            'type' => "booking_{$key}",
            'booking_ref' => $booking->reference_number,
            'status' => $status->value,
        ];

        try {
            $this->notificationService->send($booking->customer, "booking_{$key}", $title, $body, $data);
        } catch (\Throwable $e) {
            Log::debug("Failed to notify customer of status update: {$e->getMessage()}");
        }

        // If completed, also notify provider with earnings
        if ($status === BookingStatus::Completed && $booking->provider) {
            $providerLocale = $booking->provider->language ?? 'en';
            $earning = $booking->provider_earning ?? 0;

            $providerTitle = __('notifications.provider_earning_title', [], $providerLocale);
            $providerBody = __('notifications.provider_earning_body', [
                'amount' => number_format($earning),
                'currency' => $currency,
                'reference' => $booking->reference_number,
            ], $providerLocale);

            try {
                $this->notificationService->send($booking->provider, 'job_completed', $providerTitle, $providerBody, [
                    'type' => 'job_completed',
                    'booking_ref' => $booking->reference_number,
                    'earning' => (string) $earning,
                ]);
            } catch (\Throwable $e) {
                Log::debug("Failed to notify provider of completion: {$e->getMessage()}");
            }
        }
    }
}
