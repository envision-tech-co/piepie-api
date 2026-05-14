<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\BookingWebController;
use App\Http\Controllers\Admin\CommissionController as AdminCommissionController;
use App\Http\Controllers\Admin\CommissionWebController;
use App\Http\Controllers\Admin\CustomerWebController;
use App\Http\Controllers\Admin\ProviderWebController;
use App\Http\Controllers\Admin\ServiceCategoryController as AdminServiceCategoryController;
use App\Http\Controllers\Admin\ServiceCategoryWebController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:admin')->group(function () {
        Route::post('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
        Route::get('dashboard', [AdminAuthController::class, 'dashboard'])->name('admin.dashboard');

        // --- Admin Web Panel (HTML views) ---

        // Bookings
        Route::get('bookings', [BookingWebController::class, 'index'])->name('admin.bookings.index');
        Route::get('bookings/live', [BookingWebController::class, 'live'])->name('admin.bookings.live');
        Route::get('bookings/{reference}', [BookingWebController::class, 'show'])->name('admin.bookings.show');
        Route::post('bookings/{reference}/cancel', [BookingWebController::class, 'cancel'])->name('admin.bookings.cancel');

        // Customers
        Route::get('customers', [CustomerWebController::class, 'index'])->name('admin.customers.index');
        Route::get('customers/{id}', [CustomerWebController::class, 'show'])->name('admin.customers.show');
        Route::post('customers/{id}/toggle', [CustomerWebController::class, 'toggle'])->name('admin.customers.toggle');

        // Providers
        Route::get('providers', [ProviderWebController::class, 'index'])->name('admin.providers.index');
        Route::get('providers/{id}', [ProviderWebController::class, 'show'])->name('admin.providers.show');
        Route::post('providers/{id}/status', [ProviderWebController::class, 'updateStatus'])->name('admin.providers.status');

        // Service Categories
        Route::get('services', [ServiceCategoryWebController::class, 'index'])->name('admin.services.index');
        Route::get('services/create', [ServiceCategoryWebController::class, 'create'])->name('admin.services.create');
        Route::post('services', [ServiceCategoryWebController::class, 'store'])->name('admin.services.store');
        Route::get('services/{id}/edit', [ServiceCategoryWebController::class, 'edit'])->name('admin.services.edit');
        Route::put('services/{id}', [ServiceCategoryWebController::class, 'update'])->name('admin.services.update');
        Route::post('services/{id}/toggle', [ServiceCategoryWebController::class, 'toggle'])->name('admin.services.toggle');
        Route::delete('services/{id}', [ServiceCategoryWebController::class, 'destroy'])->name('admin.services.destroy');

        // Commissions
        Route::get('commissions', [CommissionWebController::class, 'index'])->name('admin.commissions.index');
        Route::post('commissions', [CommissionWebController::class, 'store'])->name('admin.commissions.store');
        Route::delete('commissions/{id}', [CommissionWebController::class, 'destroy'])->name('admin.commissions.destroy');

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
