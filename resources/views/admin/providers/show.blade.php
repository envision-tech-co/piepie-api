@extends('admin.layouts.app')

@section('title', $provider->name ?? 'Provider')
@section('page_title', 'Provider: ' . ($provider->name ?? $provider->phone))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        {{-- Profile --}}
        <div class="bg-white rounded-xl shadow-sm border p-6">
            <div class="flex items-start justify-between mb-5">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-bold text-xl">
                        {{ strtoupper(substr($provider->name ?? '?', 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900">{{ $provider->name ?? '(no name)' }}</h3>
                        <div class="text-sm text-gray-500 font-mono mt-0.5">{{ $provider->phone }}</div>
                    </div>
                </div>
                @include('admin.partials.status_badge', ['status' => $provider->status])
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-x-6 gap-y-4 text-sm">
                <div>
                    <div class="text-xs text-gray-500 font-medium">Vehicle Type</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ ucfirst($provider->vehicle_type ?? '—') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Speciality</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ $provider->service_speciality ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Language</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ strtoupper($provider->language) }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Online Status</div>
                    <div class="mt-0.5">
                        @if ($provider->is_online)
                            <span class="inline-flex items-center gap-1 text-green-600 text-sm font-medium">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online
                            </span>
                        @else
                            <span class="text-gray-400 text-sm">Offline</span>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Rating</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ number_format((float) $provider->overall_rating, 1) }} <span class="text-yellow-500">★</span></div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Total Jobs</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ $provider->total_jobs }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Registered</div>
                    <div class="font-semibold text-gray-900 mt-0.5">{{ $provider->created_at->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-500 font-medium">Current Location</div>
                    <div class="font-mono text-xs text-gray-700 mt-0.5">
                        @if ($provider->current_lat && $provider->current_lng)
                            {{ (float) $provider->current_lat }}, {{ (float) $provider->current_lng }}
                        @else
                            —
                        @endif
                    </div>
                </div>
            </div>

            @if ($provider->id_document_path)
                <div class="mt-5 pt-5 border-t">
                    <div class="text-xs text-gray-500 font-medium mb-2">ID Document</div>
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($provider->id_document_path) }}"
                       target="_blank"
                       class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700 font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        View document
                    </a>
                </div>
            @endif
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
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Earning</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($provider->bookings as $b)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('admin.bookings.show', $b->reference_number) }}" class="text-primary-600 font-mono text-xs font-medium hover:underline">
                                        {{ $b->reference_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-3 text-sm">{{ $b->serviceCategory->name_en ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    @include('admin.partials.status_badge', ['status' => $b->status->value, 'label' => $b->status->label()])
                                </td>
                                <td class="px-6 py-3 text-sm text-right font-medium">{{ $b->provider_earning ? number_format($b->provider_earning) : '—' }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500">{{ $b->created_at->format('M d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">No bookings yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-6">
        {{-- Stats --}}
        <div class="bg-white rounded-xl shadow-sm border p-5">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Performance</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Completed Jobs</span>
                    <span class="text-xl font-bold text-green-600">{{ $stats['total_jobs'] }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Active Jobs</span>
                    <span class="text-xl font-bold text-primary-600">{{ $stats['active_jobs'] }}</span>
                </div>
                <div class="pt-3 border-t">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Earnings</span>
                        <span class="text-lg font-bold text-emerald-600">{{ number_format($stats['total_earnings']) }} <span class="text-xs font-normal text-gray-500">{{ config('pippip.currency') }}</span></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Status actions --}}
        <div class="bg-white rounded-xl shadow-sm border p-5" x-data="{ confirmAction: null }">
            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Change Status</h3>
            <div class="space-y-2">
                @php
                    $statusButtons = [
                        'approved' => ['color' => 'bg-green-600 hover:bg-green-700', 'icon' => '✓'],
                        'rejected' => ['color' => 'bg-red-600 hover:bg-red-700', 'icon' => '✗'],
                        'suspended' => ['color' => 'bg-gray-600 hover:bg-gray-700', 'icon' => '⊘'],
                        'pending' => ['color' => 'bg-yellow-600 hover:bg-yellow-700', 'icon' => '⏳'],
                    ];
                @endphp

                @foreach ($statusButtons as $status => $config)
                    @if ($provider->status !== $status)
                        <form action="{{ route('admin.providers.status', $provider->id) }}" method="POST"
                            x-data @submit.prevent="if(confirm('Change status to {{ $status }}?')) $el.submit()">
                            @csrf
                            <input type="hidden" name="status" value="{{ $status }}">
                            <button type="submit" class="w-full {{ $config['color'] }} text-white py-2.5 rounded-lg text-sm font-medium transition flex items-center justify-center gap-2">
                                <span>{{ $config['icon'] }}</span>
                                Mark as {{ ucfirst($status) }}
                            </button>
                        </form>
                    @endif
                @endforeach
            </div>

            <div class="mt-4 pt-4 border-t text-xs text-gray-500">
                Current status: <strong class="capitalize">{{ $provider->status }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
