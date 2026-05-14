@extends('admin.layouts.app')

@section('title', $customer->name ?? 'Customer')
@section('page_title', 'Customer: ' . ($customer->name ?? $customer->phone))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Profile card --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-bold text-xl">
                        {{ strtoupper(substr($customer->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $customer->name ?? '(no name)' }}</h3>
                        <div class="text-sm text-gray-500 font-mono mt-0.5">{{ $customer->phone }}</div>
                        <div class="text-xs text-gray-400 mt-1">
                            Joined {{ $customer->created_at->format('M d, Y') }} · Language: {{ strtoupper($customer->language) }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.customers.toggle', $customer->id) }}" method="POST" class="inline-flex items-center gap-2">
                        @csrf
                        <span class="text-xs text-gray-500">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span>
                        <label class="toggle-switch">
                            <input type="checkbox" {{ $customer->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                            <span class="toggle-slider"></span>
                        </label>
                    </form>
                </div>
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Recent Bookings</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reference</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Provider</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($customer->bookings as $b)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('admin.bookings.show', $b->reference_number) }}" class="text-primary-600 font-mono text-xs font-medium hover:underline">
                                        {{ $b->reference_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-sm">{{ $b->serviceCategory->name_en ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm">{{ $b->provider->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    @include('admin.partials.status_badge', ['status' => $b->status->value, 'label' => $b->status->label()])
                                </td>
                                <td class="px-6 py-3 text-sm font-medium">{{ number_format($b->final_price ?? $b->estimated_price) }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $b->created_at->format('M d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Stats sidebar --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Statistics</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Total Bookings</span>
                    <span class="text-lg font-bold text-gray-900">{{ $stats['total_bookings'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed</span>
                    <span class="text-lg font-bold text-green-600">{{ $stats['completed_bookings'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Cancelled</span>
                    <span class="text-lg font-bold text-red-600">{{ $stats['cancelled_bookings'] }}</span>
                </div>
                <div class="pt-3 border-t">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Spent</span>
                        <span class="text-lg font-bold text-emerald-600">{{ number_format($stats['total_spent']) }} <span class="text-xs font-normal text-gray-500">{{ config('pippip.currency') }}</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
