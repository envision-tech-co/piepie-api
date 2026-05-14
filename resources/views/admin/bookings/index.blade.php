@extends('admin.layouts.app')

@section('title', 'Bookings')
@section('page_title', 'Bookings')

@section('content')
<div class="bg-white rounded-lg shadow mb-6">
    <form method="GET" class="p-4 grid grid-cols-1 md:grid-cols-6 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ref # or customer"
            class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 md:col-span-2">

        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">All Statuses</option>
            @foreach (\App\Enums\BookingStatus::cases() as $s)
                <option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>
            @endforeach
        </select>

        <select name="service_category_id" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">All Categories</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" {{ (string) request('service_category_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name_en }}</option>
            @endforeach
        </select>

        <input type="date" name="from_date" value="{{ request('from_date') }}" class="px-3 py-2 border rounded-lg text-sm">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="px-3 py-2 border rounded-lg text-sm">

        <div class="md:col-span-6 flex gap-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
            <a href="{{ route('admin.bookings.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 text-sm">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
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
                        <td class="px-6 py-4 text-sm">{{ number_format($booking->final_price ?? $booking->estimated_price) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $booking->created_at->format('M d, H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t">
        {{ $bookings->links() }}
    </div>
</div>
@endsection
