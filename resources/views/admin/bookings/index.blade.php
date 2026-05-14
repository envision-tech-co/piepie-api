@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page_title', 'Bookings')

@section('content')
{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border mb-4">
    <form method="GET" class="p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <div class="lg:col-span-2">
                <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Reference # or customer name"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach (\App\Enums\BookingStatus::cases() as $s)
                        <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">Category</label>
                <select name="service_category_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                    <option value="">All</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" {{ (string) request('service_category_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">From</label>
                <input type="date" name="from_date" value="{{ request('from_date') }}"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-500 mb-1">To</label>
                <input type="date" name="to_date" value="{{ request('to_date') }}"
                    class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
            </div>
        </div>

        <div class="mt-3 flex gap-2">
            <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Apply Filters</button>
            <a href="{{ route('admin.bookings.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Reset</a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Service</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($bookings as $booking)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-3.5">
                            <a href="{{ route('admin.bookings.show', $booking->reference_number) }}" class="text-primary-600 font-mono text-sm font-medium hover:underline">
                                {{ $booking->reference_number }}
                            </a>
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $booking->customer->name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $booking->provider->name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-sm text-gray-700">{{ $booking->serviceCategory->name_en ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-center">
                            @include('admin.partials.status_badge', ['status' => $booking->status->value, 'label' => $booking->status->label()])
                        </td>
                        <td class="px-6 py-3.5 text-sm text-right font-medium text-gray-900">
                            {{ number_format($booking->final_price ?? $booking->estimated_price) }}
                        </td>
                        <td class="px-6 py-3.5 text-sm text-gray-500">{{ $booking->created_at->format('M d, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-400">No bookings found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($bookings->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50/50">{{ $bookings->links() }}</div>
    @endif
</div>
@endsection
