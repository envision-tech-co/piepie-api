<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\BookingType;
use App\Enums\CancelledBy;
use App\Enums\OfferStatus;
use App\Events\BookingAccepted;
use App\Events\BookingDispatched;
use App\Events\BookingStatusUpdated;
use App\Events\NoProvidersAvailable;
use App\Events\ProviderAssigned;
use App\Models\Booking;
use App\Models\BookingStatusLog;
use App\Models\CommissionSetting;
use App\Models\Customer;
use App\Models\ProviderJobOffer;
use App\Models\ProviderLocation;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BookingService
{
    public function __construct(
        protected ProximityService $proximityService
    ) {}

    /**
     * Create a new booking.
     */
    public function createBooking(Customer $customer, array $data): Booking
    {
        $category = ServiceCategory::active()->findOrFail($data['service_category_id']);

        $commissionRate = $this->getCommissionRate($category->id);

        $booking = DB::transaction(function () use ($customer, $data, $category, $commissionRate) {
            $booking = Booking::create([
                'customer_id' => $customer->id,
                'service_category_id' => $category->id,
                'status' => BookingStatus::Pending,
                'booking_type' => $data['booking_type'] ?? BookingType::Immediate->value,
                'scheduled_at' => $data['scheduled_at'] ?? null,
                'customer_lat' => $data['customer_lat'],
                'customer_lng' => $data['customer_lng'],
                'customer_address' => $data['customer_address'],
                'customer_notes' => $data['customer_notes'] ?? null,
                'estimated_price' => $category->base_price,
                'commission_rate' => $commissionRate,
                'payment_method' => $data['payment_method'] ?? 'cash',
            ]);

            // Log initial status
            $this->logStatusChange($booking, null, BookingStatus::Pending, $customer);

            return $booking;
        });

        // If immediate booking, dispatch to nearby providers
        if ($booking->booking_type === BookingType::Immediate) {
            $this->dispatchToNearbyProviders($booking);
        }

        return $booking->fresh(['customer', 'serviceCategory', 'statusLogs']);
    }

    /**
     * Dispatch booking to nearby providers.
     */
    public function dispatchToNearbyProviders(Booking $booking): void
    {
        $radiusKm = config('pippip.dispatch_radius_km', 10);
        $maxProviders = config('pippip.dispatch_max_providers', 5);
        $offerExpiry = config('pippip.offer_expiry_seconds', 30);

        $providers = $this->proximityService->findNearbyProviders(
            (float) $booking->customer_lat,
            (float) $booking->customer_lng,
            $radiusKm,
            $maxProviders
        );

        if ($providers->isEmpty()) {
            // Update status to searching even with no providers
            $booking->update(['status' => BookingStatus::Searching]);
            $this->logStatusChange($booking, BookingStatus::Pending, BookingStatus::Searching);
            event(new NoProvidersAvailable($booking));
            return;
        }

        DB::transaction(function () use ($booking, $providers, $offerExpiry) {
            $now = now();
            $expiresAt = $now->copy()->addSeconds($offerExpiry);

            foreach ($providers as $provider) {
                ProviderJobOffer::create([
                    'booking_id' => $booking->id,
                    'provider_id' => $provider->id,
                    'status' => OfferStatus::Pending,
                    'offered_at' => $now,
                    'expires_at' => $expiresAt,
                ]);
            }

            $booking->update(['status' => BookingStatus::Searching]);
            $this->logStatusChange($booking, BookingStatus::Pending, BookingStatus::Searching);
        });

        event(new BookingDispatched($booking, $providers->pluck('id')->toArray()));
    }

    /**
     * Accept a booking by a provider.
     */
    public function acceptBooking(ServiceProvider $provider, Booking $booking): Booking
    {
        if ($booking->status !== BookingStatus::Searching) {
            throw new InvalidArgumentException('Booking is not in searching status.');
        }

        $offer = ProviderJobOffer::where('booking_id', $booking->id)
            ->where('provider_id', $provider->id)
            ->where('status', OfferStatus::Pending)
            ->where('expires_at', '>', now())
            ->first();

        if (!$offer) {
            throw new InvalidArgumentException('No valid pending offer found for this provider.');
        }

        return DB::transaction(function () use ($booking, $provider, $offer) {
            // Mark this offer as accepted
            $offer->update([
                'status' => OfferStatus::Accepted,
                'responded_at' => now(),
            ]);

            // Expire all other offers for this booking
            ProviderJobOffer::where('booking_id', $booking->id)
                ->where('id', '!=', $offer->id)
                ->whereIn('status', [OfferStatus::Pending->value])
                ->update([
                    'status' => OfferStatus::Expired,
                    'responded_at' => now(),
                ]);

            // Update booking
            $booking->update([
                'status' => BookingStatus::Accepted,
                'provider_id' => $provider->id,
                'accepted_at' => now(),
            ]);

            $this->logStatusChange($booking, BookingStatus::Searching, BookingStatus::Accepted, $provider);

            event(new BookingAccepted($booking));
            event(new ProviderAssigned($booking));

            return $booking->fresh(['customer', 'provider', 'serviceCategory', 'statusLogs']);
        });
    }

    /**
     * Update booking status with state machine validation.
     */
    public function updateStatus(Booking $booking, BookingStatus $newStatus, array $context = []): Booking
    {
        $currentStatus = $booking->status;

        if (!$currentStatus->canTransitionTo($newStatus)) {
            throw new InvalidArgumentException(
                "Cannot transition from {$currentStatus->value} to {$newStatus->value}."
            );
        }

        return DB::transaction(function () use ($booking, $newStatus, $currentStatus, $context) {
            $updateData = ['status' => $newStatus];

            // Update relevant timestamp
            match ($newStatus) {
                BookingStatus::Accepted => $updateData['accepted_at'] = now(),
                BookingStatus::Arrived => $updateData['arrived_at'] = now(),
                BookingStatus::InProgress => $updateData['started_at'] = now(),
                BookingStatus::Completed => $updateData['completed_at'] = now(),
                BookingStatus::Cancelled => $updateData['cancelled_at'] = now(),
                default => null,
            };

            // Update provider location if provided
            if (isset($context['lat']) && isset($context['lng'])) {
                $updateData['provider_lat'] = $context['lat'];
                $updateData['provider_lng'] = $context['lng'];
            }

            // If on_the_way, also attach current provider location from provider_locations table
            if ($newStatus === BookingStatus::OnTheWay && $booking->provider_id) {
                $providerLoc = ProviderLocation::where('provider_id', $booking->provider_id)->first();
                if ($providerLoc && !isset($context['lat'])) {
                    $updateData['provider_lat'] = $providerLoc->lat;
                    $updateData['provider_lng'] = $providerLoc->lng;
                }
            }

            // Calculate financials on completion
            if ($newStatus === BookingStatus::Completed) {
                $finalPrice = $booking->estimated_price; // Use estimated as final for now
                $commissionAmount = $finalPrice * ($booking->commission_rate / 100);
                $providerEarning = $finalPrice - $commissionAmount;

                $updateData['final_price'] = $finalPrice;
                $updateData['commission_amount'] = $commissionAmount;
                $updateData['provider_earning'] = $providerEarning;
            }

            $booking->update($updateData);

            $actor = $context['actor'] ?? null;
            $this->logStatusChange(
                $booking,
                $currentStatus,
                $newStatus,
                $actor,
                $context['notes'] ?? null,
                $context['lat'] ?? null,
                $context['lng'] ?? null
            );

            event(new BookingStatusUpdated($booking, $currentStatus));

            return $booking->fresh(['customer', 'provider', 'serviceCategory', 'statusLogs']);
        });
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Booking $booking, string $cancelledBy, ?string $reason, $actor): Booking
    {
        if ($booking->status->isTerminal()) {
            throw new InvalidArgumentException('Booking is already in a terminal state.');
        }

        $booking->update([
            'cancelled_by' => $cancelledBy,
            'cancellation_reason' => $reason,
        ]);

        return $this->updateStatus($booking, BookingStatus::Cancelled, [
            'actor' => $actor,
            'notes' => $reason,
        ]);
    }

    /**
     * Get the commission rate for a service category.
     */
    public function getCommissionRate(int $serviceCategoryId): float
    {
        // Check category-specific rate
        $categorySetting = CommissionSetting::where('service_category_id', $serviceCategoryId)
            ->where('is_active', true)
            ->first();

        if ($categorySetting) {
            return (float) $categorySetting->rate;
        }

        // Check global rate (service_category_id = null)
        $globalSetting = CommissionSetting::whereNull('service_category_id')
            ->where('is_active', true)
            ->first();

        if ($globalSetting) {
            return (float) $globalSetting->rate;
        }

        // Fall back to config default
        return (float) config('pippip.default_commission', 20.0);
    }

    /**
     * Log a status change.
     */
    protected function logStatusChange(
        Booking $booking,
        ?BookingStatus $fromStatus,
        BookingStatus $toStatus,
        $actor = null,
        ?string $notes = null,
        ?float $lat = null,
        ?float $lng = null
    ): void {
        BookingStatusLog::create([
            'booking_id' => $booking->id,
            'changed_by_type' => $actor ? get_class($actor) : null,
            'changed_by_id' => $actor?->id,
            'from_status' => $fromStatus?->value,
            'to_status' => $toStatus->value,
            'notes' => $notes,
            'location_lat' => $lat,
            'location_lng' => $lng,
        ]);
    }
}
