@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
{{-- Stats cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <a href="{{ route('admin.customers.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 font-medium">Customers</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['customers']) }}</div>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center group-hover:bg-blue-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.providers.index') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 font-medium">Providers</div>
                <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['providers']) }}</div>
                <div class="text-xs text-green-600 mt-1 font-medium">{{ $stats['online_providers'] }} online now</div>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center group-hover:bg-green-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.providers.index') }}?status=pending" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow group {{ $stats['pending_providers'] > 0 ? 'ring-2 ring-yellow-200' : '' }}">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 font-medium">Pending Approvals</div>
                <div class="text-2xl font-bold {{ $stats['pending_providers'] > 0 ? 'text-yellow-600' : 'text-gray-900' }} mt-1">{{ number_format($stats['pending_providers']) }}</div>
                @if ($stats['pending_providers'] > 0)
                    <div class="text-xs text-yellow-600 mt-1 font-medium">Needs attention →</div>
                @endif
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center group-hover:bg-yellow-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </a>

    <a href="{{ route('admin.bookings.live') }}" class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow group">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm text-gray-500 font-medium">Active Bookings</div>
                <div class="text-2xl font-bold text-primary-600 mt-1">{{ number_format($stats['active_bookings']) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $stats['total_bookings'] }} total</div>
            </div>
            <div class="w-12 h-12 bg-primary-50 rounded-xl flex items-center justify-center group-hover:bg-primary-100 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
        </div>
    </a>
</div>

{{-- Revenue + Completed row --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-sm p-5 text-white">
        <div class="text-sm text-emerald-100 font-medium">Total Commission Earned</div>
        <div class="text-2xl font-bold mt-1">{{ number_format($stats['revenue_total']) }} <span class="text-sm font-normal text-emerald-200">{{ config('pippip.currency') }}</span></div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="text-sm text-gray-500 font-medium">Completed Bookings</div>
        <div class="text-2xl font-bold text-green-600 mt-1">{{ number_format($stats['completed_bookings']) }}</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm border p-5">
        <div class="text-sm text-gray-500 font-medium">Service Categories</div>
        <div class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['service_categories']) }}</div>
    </div>
</div>

{{-- Recent bookings --}}
<div class="bg-white rounded-xl shadow-sm border">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">Recent Bookings</h3>
        <a href="{{ route('admin.bookings.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">View all →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($recentBookings as $booking)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <a href="{{ route('admin.bookings.show', $booking->reference_number) }}" class="text-primary-600 font-mono text-sm font-medium hover:underline">
                                {{ $booking->reference_number }}
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $booking->customer->name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $booking->serviceCategory->name ?? '—' }}</td>
                        <td class="px-6 py-3.5">
                            @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-500">{{ $booking->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">No bookings yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
