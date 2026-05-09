<?php

use App\Http\Controllers\Api\Customer\AuthController as CustomerAuthController;
use App\Http\Controllers\Api\Provider\AuthController as ProviderAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Customer Auth Routes
Route::prefix('customer/auth')->group(function () {
    Route::post('send-otp', [CustomerAuthController::class, 'sendOtp'])
        ->middleware('throttle:5,10'); // 5 requests per 10 minutes
    
    Route::post('verify-otp', [CustomerAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1'); // 10 requests per minute
    
    Route::middleware(['auth:sanctum', 'ability:customer'])->group(function () {
        Route::post('complete-profile', [CustomerAuthController::class, 'completeProfile']);
        Route::get('me', [CustomerAuthController::class, 'me']);
        Route::post('logout', [CustomerAuthController::class, 'logout']);
    });
});

// Provider Auth Routes
Route::prefix('provider/auth')->group(function () {
    Route::post('send-otp', [ProviderAuthController::class, 'sendOtp'])
        ->middleware('throttle:5,10'); // 5 requests per 10 minutes
    
    Route::post('verify-otp', [ProviderAuthController::class, 'verifyOtp'])
        ->middleware('throttle:10,1'); // 10 requests per minute
    
    Route::middleware(['auth:sanctum', 'ability:provider'])->group(function () {
        Route::post('register', [ProviderAuthController::class, 'register']);
        Route::get('me', [ProviderAuthController::class, 'me']);
        Route::post('logout', [ProviderAuthController::class, 'logout']);
    });
});
