<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\ServiceCategoryController as AdminServiceCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Auth Routes
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

        // Admin API — JSON responses
        Route::prefix('api')->group(function () {
            // Bookings
            Route::get('bookings/live', [AdminBookingController::class, 'live']);
            Route::get('bookings', [AdminBookingController::class, 'index']);
            Route::get('bookings/{reference}', [AdminBookingController::class, 'show']);
            Route::post('bookings/{reference}/cancel', [AdminBookingController::class, 'cancel']);

            // Service Categories
            Route::get('services', [AdminServiceCategoryController::class, 'index']);
            Route::post('services', [AdminServiceCategoryController::class, 'store']);
            Route::put('services/{id}', [AdminServiceCategoryController::class, 'update']);
            Route::patch('services/{id}/toggle', [AdminServiceCategoryController::class, 'toggle']);

            // Commissions
            Route::get('commissions', [AdminCommissionController::class, 'index']);
            Route::post('commissions', [AdminCommissionController::class, 'store']);
            Route::delete('commissions/{id}', [AdminCommissionController::class, 'destroy']);
        });
    });
});
