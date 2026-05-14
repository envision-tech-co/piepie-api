@extends('admin.layouts.app')

@section('title', $provider->name ?? 'Provider')
@section('page_title', 'Provider: ' . ($provider->name ?? $provider->phone))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h3 class="text-xl font-semibold">{{ $provider->name ?? '(no name)' }}</h3>
                    <div class="text-sm text-gray-600 mt-1">{{ $provider->phone }}</div>
                </div>
                <div>
                    @include('admin.partials.status_badge', ['status' => $provider->status])
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 text-sm">
                <div>
                    <div class="text-xs text-gray-500">Vehicle Type</div>
                    <div class="font-medium">{{ $provider->vehicle_type ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Speciality</div>
                    <div class="font-medium">{{ $provider->service_speciality ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Language</div>
                    <div class="font-medium">{{ strtoupper($provider->language) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Online</div>
                    <div class="font-medium">{{ $provider->is_online ? 'Yes' : 'No' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Rating</div>
                    <div class="font-medium">⭐ {{ (float) $provider->overall_rating }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Total Jobs</div>
                    <div class="font-medium">{{ $provider->total_jobs }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Registered</div>
                    <div class="font-medium">{{ $provider->created_at->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Current Location</div>
                    <div class="font-mono text-xs">
                        @if ($provider->current_lat && $provider->current_lng)
                            {{ (float) $provider->current_lat }}, {{ (float) $provider->current_lng }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>

            @if ($provider->id_document_path)
                <div class="mt-6 pt-6 border-t">
                    <div class="text-xs text-gray-500 mb-2">ID Document</div>
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($provider->id_document_path) }}"
                       target="_blank"
                       class="text-sm text-indigo-600 hover:underline">View document →</a>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Recent Bookings</h3>
            <table class="min-w-full">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-3 py-2 text-left">Reference</th>
                        <th class="px-3 py-2 text-left">Service</th>
                        <th class="px-3 py-2 text-left">Status</th>
                        <th class="px-3 py-2 text-left">Earning</th>
                        <th class="px-3 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y text-sm">
                    @forelse ($provider->bookings as $b)
                        <tr>
                            <td class="px-3 py-2">
                                <a href="{{ route('admin.bookings.show', $b->reference_number) }}" class="text-indigo-600 font-mono text-xs hover:underline">
                                    {{ $b->reference_number }}
                                </a>
                            </td>
                            <td class="px-3 py-2">{{ $b->serviceCategory->name_en ?? '—' }}</td>
                            <td class="px-3 py-2">
                                @include('admin.partials.status_badge', ['status' => $b->status->value, 'label' => $b->status->label()])
                            </td>
                            <td class="px-3 py-2">{{ $b->provider_earning ? number_format($b->provider_earning) : '—' }}</td>
                            <td class="px-3 py-2 text-gray-500">{{ $b->created_at->format('M d') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">No bookings yet.</td></tr>
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
                    <div class="text-xs text-gray-500">Completed Jobs</div>
                    <div class="text-2xl font-bold text-green-600">{{ $stats['total_jobs'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Active Jobs</div>
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['active_jobs'] }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500">Total Earnings</div>
                    <div class="text-xl font-bold text-emerald-600">{{ number_format($stats['total_earnings']) }} {{ config('pippip.currency') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3">Change Status</h3>
            <form action="{{ route('admin.providers.status', $provider->id) }}" method="POST" class="space-y-2">
                @csrf
                @foreach (['approved' => 'bg-green-600 hover:bg-green-700', 'rejected' => 'bg-red-600 hover:bg-red-700', 'suspended' => 'bg-gray-600 hover:bg-gray-700', 'pending' => 'bg-yellow-600 hover:bg-yellow-700'] as $status => $color)
                    @if ($provider->status !== $status)
                        <button name="status" value="{{ $status }}" class="w-full {{ $color }} text-white py-2 rounded-lg text-sm capitalize">
                            Mark as {{ $status }}
                        </button>
                    @endif
                @endforeach
            </form>
        </div>
    </div>
</div>
@endsection
