<?php

use App\Models\Admin;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// Customer tracks their own booking
Broadcast::channel('booking.{reference}', function ($user, $reference) {
    $booking = Booking::where('reference_number', $reference)->first();
    if (!$booking) return false;

    if ($user instanceof Customer) return $booking->customer_id === $user->id;
    if ($user instanceof ServiceProvider) return $booking->provider_id === $user->id;

    return false;
});

// Provider's personal channel for incoming job offers
Broadcast::channel('provider.{providerId}', function ($user, $providerId) {
    return $user instanceof ServiceProvider && $user->id === (int) $providerId;
});

// Admin live dashboard
Broadcast::channel('admin.live', function ($user) {
    return $user instanceof Admin;
});
