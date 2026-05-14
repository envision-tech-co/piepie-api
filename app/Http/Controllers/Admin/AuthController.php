<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm(): View
    {
        return view('admin.auth.login');
    }

    /**
     * Handle admin login request.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $admin = Auth::guard('admin')->user();

            // Check if admin is active
            if (!$admin->is_active) {
                Auth::guard('admin')->logout();
                return back()->withErrors([
                    'email' => 'Your account has been deactivated.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard(): View
    {
        $stats = [
            'customers' => \App\Models\Customer::count(),
            'providers' => \App\Models\ServiceProvider::count(),
            'pending_providers' => \App\Models\ServiceProvider::where('status', 'pending')->count(),
            'online_providers' => \App\Models\ServiceProvider::where('is_online', true)->count(),
            'total_bookings' => \App\Models\Booking::count(),
            'active_bookings' => \App\Models\Booking::active()->count(),
            'completed_bookings' => \App\Models\Booking::where('status', 'completed')->count(),
            'revenue_total' => (float) \App\Models\Booking::where('status', 'completed')->sum('commission_amount'),
            'service_categories' => \App\Models\ServiceCategory::count(),
        ];

        $recentBookings = \App\Models\Booking::with(['customer', 'provider', 'serviceCategory'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentBookings'));
    }
}
