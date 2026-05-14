@extends('admin.layouts.app')

@section('title', $customer->name ?? 'Customer')
@section('page_title', 'Customer: ' . ($customer->name ?? $customer->phone))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-xl font-semibold">{{ $customer->name ?? '(no name)' }}</h3>
                    <div class="text-sm text-gray-600 mt-1 font-mono">{{ $customer->phone }}</div>
                    <div class="text-xs text-gray-500 mt-1">
                        Joined {{ $customer->created_at->format('M d, Y') }} · Language: {{ strtoupper($customer->language) }}
                    </div>
                </div>
                <div>
                    @if ($customer->is_active)
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                    @else
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                    @endif
                </div>
            </div>

            <div class="mt-4 pt-4 border-t">
                <form action="{{ route('admin.customers.toggle', $customer->id) }}" method="POST" class="inline">
                    @csrf
                    <button class="bg-yellow-600 hover:bg-yellow-700 text-white text-sm px-4 py-2 rounded-lg">
                        {{ $customer->is_active ? 'Deactivate Account' : 'Activate Account' }}
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Bookings</h3>
            <table class="min-w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Reference</th>
                        <th class="px-3 py-2 text-left">Service</th>
                        <th class="px-3 py-2 text-left">Provider</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Price</th>
                        <th class="px-3 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse ($customer->bookings as $b)
                        <tr>
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.bookings.show', $b->reference_number) }}" class="text-indigo-600 font-mono text-xs hover:underline">
                                    {{ $b->reference_number }}
                                </a>
                            </td>
                            <td class="px-3 py-2">{{ $b->serviceCategory->name_en ?? '—' }}</td>
                            <td class="px-3 py-2">{{ $b->provider->name ?? '—' }}</td>
                            <td class="px-3 py-2">
                                @include('admin.partials.status_badge', ['status' => $b->status->value, 'label' => $b->status->label()])
                            </td>
                            <td class="px-3 py-2">{{ number_format($b->final_price ?? $b->estimated_price) }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $b->created_at->format('M d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">No bookings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Stats</h3>
            <div class="space-y-3">
                <div>
                    <div class="text-xs text-gray-500">Total Bookings</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $stats['total_bookings'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Completed</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['completed_bookings'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Cancelled</div>
                    <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled_bookings'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Total Spent</div>
                    <div class="text-xl font-bold text-emerald-600">{{ number_format($stats['total_spent']) }} {{ config('pippip.currency') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
