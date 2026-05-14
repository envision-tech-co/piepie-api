<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $customers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    public function show(int $id): View
    {
        $customer = Customer::with(['bookings' => fn ($q) => $q->latest()->limit(15)->with(['serviceCategory', 'provider'])])
            ->findOrFail($id);

        $stats = [
            'total_bookings' => $customer->bookings()->count(),
            'completed_bookings' => $customer->bookings()->where('status', 'completed')->count(),
            'cancelled_bookings' => $customer->bookings()->where('status', 'cancelled')->count(),
            'total_spent' => (float) $customer->bookings()->where('status', 'completed')->sum('final_price'),
        ];

        return view('admin.customers.show', compact('customer', 'stats'));
    }

    public function toggle(int $id): RedirectResponse
    {
        $customer = Customer::findOrFail($id);
        $customer->update(['is_active' => !$customer->is_active]);

        return back()->with('success', $customer->is_active ? 'Customer activated.' : 'Customer deactivated.');
    }
}
