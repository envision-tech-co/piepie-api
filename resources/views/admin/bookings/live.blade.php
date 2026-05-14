@extends('admin.layouts.app')

@section('title', 'Live Bookings')
@section('page_title', 'Live Bookings (' . $bookings->count() . ')')

@push('head')
    <meta http-equiv="refresh" content="30">
@endpush

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-800">All Non-Terminal Bookings</h3>
            <p class="text-xs text-gray-500 mt-1">Auto-refreshes every 30 seconds.</p>
        </div>
        <button onclick="location.reload()" class="text-sm text-indigo-600 hover:underline">Refresh now</button>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.bookings.show', $booking->reference_number) }}" class="text-indigo-600 font-mono text-sm hover:underline">
                                {{ $booking->reference_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm">{{ $booking->customer->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $booking->provider->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $booking->serviceCategory->name_en ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
                        </td>
                        <td class="px-6 py-4 text-xs font-mono text-gray-500">
                            @if ($booking->provider_lat)
                                P: {{ (float) $booking->provider_lat }}, {{ (float) $booking->provider_lng }}<br>
                            @endif
                            C: {{ (float) $booking->customer_lat }}, {{ (float) $booking->customer_lng }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $booking->created_at->diffForHumans() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No live bookings right now.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
