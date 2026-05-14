@extends('admin.layouts.app')

@section('title', $booking->reference_number)
@section('page_title', 'Booking: ' . $booking->reference_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Summary --}}
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <div class="text-xs text-gray-500">Reference</div>
                    <div class="font-mono text-lg">{{ $booking->reference_number }}</div>
                </div>
                <div>
                    @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <div class="text-xs text-gray-500">Type</div>
                    <div class="font-medium">{{ ucfirst($booking->booking_type->value) }}</div>
                </div>
                @if ($booking->scheduled_at)
                    <div>
                        <div class="text-xs text-gray-500">Scheduled At</div>
                        <div class="font-medium">{{ $booking->scheduled_at->format('M d, Y H:i') }}</div>
                    </div>
                @endif
                <div>
                    <div class="text-xs text-gray-500">Estimated Price</div>
                    <div class="font-medium">{{ number_format($booking->estimated_price) }} {{ config('pippip.currency') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Final Price</div>
                    <div class="font-medium">{{ $booking->final_price ? number_format($booking->final_price) : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Commission ({{ $booking->commission_rate }}%)</div>
                    <div class="font-medium">{{ $booking->commission_amount ? number_format($booking->commission_amount) : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Provider Earning</div>
                    <div class="font-medium">{{ $booking->provider_earning ? number_format($booking->provider_earning) : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Payment</div>
                    <div class="font-medium">{{ ucfirst($booking->payment_method->value) }} · {{ ucfirst($booking->payment_status->value) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Created</div>
                    <div class="font-medium">{{ $booking->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>

            @if ($booking->customer_notes)
                <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="text-xs font-semibold text-yellow-800 mb-1">Customer Notes</div>
                    <div class="text-sm text-yellow-900">{{ $booking->customer_notes }}</div>
                </div>
            @endif

            @if ($booking->cancelled_by)
                <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-3">
                    <div class="text-xs font-semibold text-red-800 mb-1">Cancelled by {{ ucfirst($booking->cancelled_by->value) }}</div>
                    <div class="text-sm text-red-900">{{ $booking->cancellation_reason ?? 'No reason given' }}</div>
                </div>
            @endif
        </div>

        {{-- Status log timeline --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Status Timeline</h3>
            <ul class="space-y-3">
                @foreach ($booking->statusLogs as $log)
                    <li class="flex gap-3">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                        </div>
                        <div class="flex-1 text-sm">
                            <div class="font-medium">
                                {{ $log->from_status ? ucwords(str_replace('_', ' ', $log->from_status)) . ' → ' : '' }}
                                {{ ucwords(str_replace('_', ' ', $log->to_status)) }}
                            </div>
                            <div class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                            @if ($log->notes)
                                <div class="text-xs text-gray-600 mt-1">{{ $log->notes }}</div>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- Job offers --}}
        @if ($booking->jobOffers->count() > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Provider Offers ({{ $booking->jobOffers->count() }})</h3>
                <table class="min-w-full">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-3 py-2 text-left">Provider</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Offered</th>
                            <th class="px-3 py-2 text-left">Responded</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-sm">
                        @foreach ($booking->jobOffers as $offer)
                            <tr>
                                <td class="px-3 py-2">
                                    <a href="{{ route('admin.providers.show', $offer->provider->id) }}" class="text-indigo-600 hover:underline">
                                        {{ $offer->provider->name ?? '—' }}
                                    </a>
                                </td>
                                <td class="px-3 py-2">
                                    @include('admin.partials.status_badge', ['status' => $offer->status->value])
                                </td>
                                <td class="px-3 py-2 text-gray-600">{{ $offer->offered_at?->format('M d, H:i:s') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $offer->responded_at?->format('M d, H:i:s') ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Side: customer / provider / location --}}
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Customer</h3>
            @if ($booking->customer)
                <div class="font-medium">{{ $booking->customer->name ?? '(no name)' }}</div>
                <div class="text-sm text-gray-600">{{ $booking->customer->phone }}</div>
                <a href="{{ route('admin.customers.show', $booking->customer->id) }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">View profile →</a>
            @else
                <div class="text-gray-500">Not assigned</div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Provider</h3>
            @if ($booking->provider)
                <div class="font-medium">{{ $booking->provider->name }}</div>
                <div class="text-sm text-gray-600">{{ $booking->provider->phone }}</div>
                <div class="text-xs text-gray-500 mt-1">⭐ {{ (float) $booking->provider->overall_rating }} · {{ $booking->provider->total_jobs }} jobs</div>
                <a href="{{ route('admin.providers.show', $booking->provider->id) }}" class="mt-2 inline-block text-xs text-indigo-600 hover:underline">View profile →</a>
            @else
                <div class="text-gray-500">Not assigned yet</div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Location</h3>
            <div class="text-sm">{{ $booking->customer_address }}</div>
            <div class="text-xs text-gray-500 mt-1 font-mono">
                {{ (float) $booking->customer_lat }}, {{ (float) $booking->customer_lng }}
            </div>
            @if ($booking->provider_lat && $booking->provider_lng)
                <div class="mt-3 pt-3 border-t text-xs">
                    <div class="font-semibold text-gray-600">Provider location</div>
                    <div class="text-gray-500 font-mono">{{ (float) $booking->provider_lat }}, {{ (float) $booking->provider_lng }}</div>
                </div>
            @endif
        </div>

        @if (!$booking->status->isTerminal())
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Actions</h3>
                <form action="{{ route('admin.bookings.cancel', $booking->reference_number) }}" method="POST"
                    onsubmit="return confirm('Force cancel this booking?');">
                    @csrf
                    <input type="text" name="reason" placeholder="Cancellation reason"
                        class="w-full px-3 py-2 border rounded-lg text-sm mb-2">
                    <button class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm">Force Cancel</button>
                </form>
            </div>
        @endif
    </div>
</div>
@endsection
