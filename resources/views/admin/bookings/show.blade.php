@extends('admin.layouts.app')

@section('title', $booking->reference_number)
@section('page_title', 'Booking: ' . $booking->reference_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Summary card --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <div class="text-xs text-gray-500 uppercase tracking-wider font-medium">Reference</div>
                    <div class="font-mono text-xl font-bold text-gray-900 mt-0.5">{{ $booking->reference_number }}</div>
                </div>
                @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 gap-x-6 gap-y-4 text-sm">
                <div>
                    <div class="text-xs text-gray-500 font-medium">Type</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ ucfirst($booking->booking_type->value) }}</div>
                </div>
                @if ($booking->scheduled_at)
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Scheduled At</div>
                        <div class="font-semibold text-gray-900 mt-0.5">{{ $booking->scheduled_at->format('M d, Y H:i') }}</div>
                    </div>
                @endif
                <div>
                    <div class="text-xs text-gray-500 font-medium">Estimated Price</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ number_format($booking->estimated_price) }} {{ config('pippip.currency') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Final Price</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ $booking->final_price ? number_format($booking->final_price) . ' ' . config('pippip.currency') : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Commission ({{ number_format($booking->commission_rate, 1) }}%)</div>
                    <div class="font-semibold text-emerald-600 mt-0.5">{{ $booking->commission_amount ? number_format($booking->commission_amount) : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Provider Earning</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ $booking->provider_earning ? number_format($booking->provider_earning) : '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Payment</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ ucfirst($booking->payment_method->value) }} · @include('admin.partials.status_badge', ['status' => $booking->payment_status->value])</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Created</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ $booking->created_at->format('M d, Y H:i') }}</div>
                </div>
            </div>

            @if ($booking->customer_notes)
                <div class="mt-5 bg-yellow-50 border border-yellow-100 rounded-lg p-4">
                    <div class="text-xs font-semibold text-yellow-800 mb-1">Customer Notes</div>
                    <div class="text-sm text-yellow-900">{{ $booking->customer_notes }}</div>
                </div>
            @endif

            @if ($booking->cancelled_by)
                <div class="mt-5 bg-red-50 border border-red-100 rounded-lg p-4">
                    <div class="text-xs font-semibold text-red-800 mb-1">Cancelled by {{ ucfirst($booking->cancelled_by->value) }}</div>
                    <div class="text-sm text-red-900">{{ $booking->cancellation_reason ?? 'No reason given' }}</div>
                    @if ($booking->cancelled_at)
                        <div class="text-xs text-red-600 mt-1">{{ $booking->cancelled_at->format('M d, Y H:i:s') }}</div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Timeline --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Timeline</h3>
            <div class="relative">
                <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-gray-200"></div>
                <ul class="space-y-4">
                    @foreach ($booking->statusLogs as $log)
                        <li class="relative flex gap-4 pl-8">
                            <div class="absolute left-0 top-1 w-6 h-6 rounded-full border-2 border-primary-300 bg-white flex items-center justify-center">
                                <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                            </div>
                            <div class="flex-1 pb-2">
                                <div class="text-sm font-medium text-gray-900">
                                    @if ($log->from_status)
                                        {{ ucwords(str_replace('_', ' ', $log->from_status)) }} →
                                    @endif
                                    {{ ucwords(str_replace('_', ' ', $log->to_status)) }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                                @if ($log->notes)
                                    <div class="text-xs text-gray-600 mt-1 bg-gray-50 rounded px-2 py-1 inline-block">{{ $log->notes }}</div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Job offers --}}
        @if ($booking->jobOffers->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Provider Offers ({{ $booking->jobOffers->count() }})</h3>
                <div class="space-y-3">
                    @foreach ($booking->jobOffers as $offer)
                        <div class="flex items-center justify-between p-3 rounded-lg border {{ $offer->status->value === 'accepted' ? 'border-green-200 bg-green-50' : 'border-gray-100' }}">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-xs font-bold text-gray-600">
                                    {{ strtoupper(substr($offer->provider->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <a href="{{ route('admin.providers.show', $offer->provider->id) }}" class="text-sm font-medium text-primary-600 hover:underline">
                                        {{ $offer->provider->name ?? '—' }}
                                    </a>
                                    <div class="text-xs text-gray-500">Offered {{ $offer->offered_at?->format('H:i:s') }}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($offer->responded_at)
                                    <span class="text-xs text-gray-500">Responded {{ $offer->responded_at->format('H:i:s') }}</span>
                                @endif
                                @include('admin.partials.status_badge', ['status' => $offer->status->value])
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Customer --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Customer</h3>
            @if ($booking->customer)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-semibold text-sm">
                        {{ strtoupper(substr($booking->customer->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $booking->customer->name ?? '(no name)' }}</div>
                        <div class="text-sm text-gray-500 font-mono">{{ $booking->customer->phone }}</div>
                    </div>
                </div>
                <a href="{{ route('admin.customers.show', $booking->customer->id) }}" class="mt-3 inline-block text-xs text-primary-600 hover:underline font-medium">View profile →</a>
            @else
                <div class="text-gray-400 text-sm">Not available</div>
            @endif
        </div>

        {{-- Provider --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Provider</h3>
            @if ($booking->provider)
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-100 text-green-700 rounded-full flex items-center justify-center font-semibold text-sm">
                        {{ strtoupper(substr($booking->provider->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $booking->provider->name }}</div>
                        <div class="text-sm text-gray-500 font-mono">{{ $booking->provider->phone }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">⭐ {{ (float) $booking->provider->overall_rating }} · {{ $booking->provider->total_jobs }} jobs</div>
                    </div>
                </div>
                <a href="{{ route('admin.providers.show', $booking->provider->id) }}" class="mt-3 inline-block text-xs text-primary-600 hover:underline font-medium">View profile →</a>
            @else
                <div class="text-gray-400 text-sm">Not assigned yet</div>
            @endif
        </div>

        {{-- Location --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Location</h3>
            <div class="text-sm text-gray-800">{{ $booking->customer_address }}</div>
            <div class="text-xs text-gray-500 font-mono mt-1">
                {{ (float) $booking->customer_lat }}, {{ (float) $booking->customer_lng }}
            </div>
            @if ($booking->provider_lat && $booking->provider_lng)
                <div class="mt-3 pt-3 border-t">
                    <div class="text-xs font-medium text-gray-600">Provider last location</div>
                    <div class="text-xs text-gray-500 font-mono mt-0.5">{{ (float) $booking->provider_lat }}, {{ (float) $booking->provider_lng }}</div>
                </div>
            @endif
        </div>

        {{-- Actions --}}
        @if (!$booking->status->isTerminal())
            <div class="bg-white rounded-xl shadow-sm border p-5" x-data="{ showCancel: false }">
                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Admin Actions</h3>
                <button @click="showCancel = !showCancel"
                    class="w-full bg-red-50 hover:bg-red-100 text-red-700 py-2.5 rounded-lg text-sm font-medium transition border border-red-200">
                    Force Cancel Booking
                </button>
                <div x-show="showCancel" x-cloak x-transition class="mt-3">
                    <form action="{{ route('admin.bookings.cancel', $booking->reference_number) }}" method="POST">
                        @csrf
                        <textarea name="reason" rows="2" placeholder="Cancellation reason (optional)"
                            class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent mb-2"></textarea>
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium transition"
                            onclick="return confirm('Are you sure you want to force cancel this booking?')">
                            Confirm Cancellation
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
