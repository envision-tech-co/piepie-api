<?php

use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Api\Provider\AuthController as ProviderAuthController;
use App\Http\Controllers\Api\Provider\BookingController as ProviderBookingController;
use App\Http\Controllers\Admin\ServiceCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public: Active service categories (no auth)
Route::get('services', [ServiceCategoryController::class, 'publicList']);

// Customer Auth Routes
Route::prefix('customer/auth')->group(function () {
    Route::post('send-otp', [CustomerAuthController::class, 'sendOtp'])
        ->middleware('throttle:5,10');

    Route::post('verify-otp', [CustomerAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'ability:customer'])->group(function () {
        Route::post('complete-profile', [CustomerAuthController::class, 'completeProfile']);
        Route::get('me', [CustomerAuthController::class, 'me']);
        Route::post('logout', [CustomerAuthController::class, 'logout']);
    });
});

// Customer Booking Routes
Route::prefix('customer/bookings')->middleware(['auth:sanctum', 'ability:customer'])->group(function () {
    Route::post('/', [CustomerBookingController::class, 'store']);
    Route::get('/', [CustomerBookingController::class, 'index']);
    Route::get('{reference}', [CustomerBookingController::class, 'show']);
    Route::post('{reference}/cancel', [CustomerBookingController::class, 'cancel']);
    Route::get('{reference}/track', [CustomerBookingController::class, 'track']);
});

// Provider Auth Routes
Route::prefix('provider/auth')->group(function () {
    Route::post('send-otp', [ProviderAuthController::class, 'sendOtp'])
        ->middleware('throttle:5,10');

    Route::post('verify-otp', [ProviderAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1');

    Route::middleware(['auth:sanctum', 'ability:provider'])->group(function () {
        Route::post('register', [ProviderAuthController::class, 'register']);
        Route::get('me', [ProviderAuthController::class, 'me']);
        Route::post('logout', [ProviderAuthController::class, 'logout']);
    });
});

// Provider Booking Routes
Route::prefix('provider/bookings')->middleware(['auth:sanctum', 'ability:provider'])->group(function () {
    Route::get('offers', [ProviderBookingController::class, 'offers']);
    Route::post('offers/{offerId}/accept', [ProviderBookingController::class, 'acceptOffer']);
    Route::post('offers/{offerId}/decline', [ProviderBookingController::class, 'declineOffer']);
    Route::get('active', [ProviderBookingController::class, 'active']);
    Route::post('{reference}/status', [ProviderBookingController::class, 'updateStatus']);
    Route::get('/', [ProviderBookingController::class, 'index']);
});
