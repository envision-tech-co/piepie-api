@extends('admin.layouts.app')

@section('title', 'Live Bookings')
@section('page_title', 'Live Bookings')

@push('head')
    <meta http-equiv="refresh" content="15">
@endpush

@section('content')
<div class="flex items-center justify-between mb-4">
    <div class="flex items-center gap-3">
        <span class="relative flex h-3 w-3">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        </span>
        <span class="text-sm text-gray-600">{{ $bookings->count() }} active booking(s) · Auto-refreshes every 15s</span>
    </div>
    <button onclick="location.reload()" class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700 font-medium">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refresh now
    </button>
</div>

@if ($bookings->isEmpty())
    <div class="bg-white rounded-xl shadow-sm border p-12 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-gray-500 font-medium text-lg">All clear</p>
        <p class="text-gray-400 text-sm mt-1">No active bookings right now.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach ($bookings as $booking)
            <a href="{{ route('admin.bookings.show', $booking->reference_number) }}"
                class="bg-white rounded-xl shadow-sm border p-5 hover:shadow-md transition-shadow group">
                <div class="flex items-start justify-between mb-3">
                    <div class="font-mono text-sm font-semibold text-primary-600 group-hover:text-primary-700">
                        {{ $booking->reference_number }}
                    </div>
                    @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
                </div>

                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 w-16 flex-shrink-0">Service</span>
                        <span class="text-gray-800 font-medium">{{ $booking->serviceCategory->name_en ?? '—' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 w-16 flex-shrink-0">Customer</span>
                        <span class="text-gray-800">{{ $booking->customer->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-gray-400 w-16 flex-shrink-0">Provider</span>
                        <span class="text-gray-800">{{ $booking->provider->name ?? 'Not assigned' }}</span>
                    </div>
                </div>

                <div class="mt-3 pt-3 border-t flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $booking->created_at->diffForHumans() }}</span>
                    @if ($booking->provider_lat && $booking->provider_lng)
                        <span class="font-mono">📍 {{ number_format((float) $booking->provider_lat, 4) }}, {{ number_format((float) $booking->provider_lng, 4) }}</span>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
