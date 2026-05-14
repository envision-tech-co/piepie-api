<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProviderWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = ServiceProvider::query();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $providers = $query->orderByDesc('created_at')->paginate(20)->withQueryString();

        $pendingCount = ServiceProvider::where('status', 'pending')->count();

        return view('admin.providers.index', compact('providers', 'pendingCount'));
    }

    public function show(int $id): View
    {
        $provider = ServiceProvider::with(['bookings' => fn ($q) => $q->latest()->limit(10)->with('serviceCategory')])
            ->findOrFail($id);

        $stats = [
            'total_jobs' => $provider->bookings()->where('status', 'completed')->count(),
            'active_jobs' => $provider->bookings()->active()->count(),
            'total_earnings' => (float) $provider->bookings()->where('status', 'completed')->sum('provider_earning'),
        ];

        return view('admin.providers.show', compact('provider', 'stats'));
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected,suspended',
        ]);

        $provider = ServiceProvider::findOrFail($id);
        $provider->update(['status' => $request->input('status')]);

        return back()->with('success', "Provider status updated to {$request->input('status')}.");
    }
}
