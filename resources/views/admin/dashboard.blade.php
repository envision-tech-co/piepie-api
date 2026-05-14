@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Customers</div>
        <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['customers']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Providers</div>
        <div class="text-3xl font-bold text-green-600">{{ number_format($stats['providers']) }}</div>
        <div class="text-xs text-gray-500 mt-1">{{ $stats['online_providers'] }} online</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Pending Provider Approvals</div>
        <div class="text-3xl font-bold text-yellow-600">{{ number_format($stats['pending_providers']) }}</div>
        @if ($stats['pending_providers'] > 0)
            <a href="{{ route('admin.providers.index') }}?status=pending" class="text-xs text-indigo-600 hover:underline">Review →</a>
        @endif
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Service Categories</div>
        <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['service_categories']) }}</div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Total Bookings</div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total_bookings']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Active Bookings</div>
        <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['active_bookings']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Completed</div>
        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['completed_bookings']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-500 mb-1">Total Commission ({{ config('pippip.currency') }})</div>
        <div class="text-2xl font-bold text-emerald-600">{{ number_format($stats['revenue_total']) }}</div>
    </div>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Recent Bookings</h3>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-indigo-600 hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($recentBookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.bookings.show', $booking->reference_number) }}" class="text-indigo-600 font-mono text-sm hover:underline">
                                {{ $booking->reference_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $booking->customer->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $booking->serviceCategory->name ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $booking->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No bookings yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
