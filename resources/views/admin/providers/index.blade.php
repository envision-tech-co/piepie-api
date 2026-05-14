@extends('admin.layouts.app')

@section('title', 'Providers')
@section('page_title', 'Service Providers')

@section('content')
@if ($pendingCount > 0)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-900 px-4 py-3 rounded-xl mb-4 flex justify-between items-center">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            <span class="font-medium">{{ $pendingCount }} provider(s) awaiting approval</span>
        </div>
        <a href="?status=pending" class="text-sm font-medium text-primary-600 hover:text-primary-700">Review pending →</a>
    </div>
@endif

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm border mb-4">
    <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or phone"
                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>

        <div class="w-40">
            <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All</option>
                @foreach (['pending', 'approved', 'rejected', 'suspended'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
        </div>

        <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
        <a href="{{ route('admin.providers.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Provider</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Speciality</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Online</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Rating</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jobs</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($providers as $p)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.providers.show', $p->id) }}" class="flex items-center gap-3 group">
                                <div class="w-9 h-9 bg-primary-100 text-primary-700 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($p->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 group-hover:text-primary-600 transition">{{ $p->name ?? '(no name)' }}</div>
                                    <div class="text-xs text-gray-500">Joined {{ $p->created_at->format('M d, Y') }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $p->phone }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $p->service_speciality ?? '—' }}</td>
                        <td class="px-6 py-4 text-center">
                            @include('admin.partials.status_badge', ['status' => $p->status])
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($p->is_online)
                                <span class="inline-flex items-center gap-1 text-green-600 text-xs font-medium">
                                    <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Online
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">Offline</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center text-sm">
                            <span class="font-medium">{{ number_format((float) $p->overall_rating, 1) }}</span>
                            <span class="text-yellow-500">★</span>
                        </td>
                        <td class="px-6 py-4 text-center text-sm font-medium text-gray-700">{{ $p->total_jobs }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.providers.show', $p->id) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-400">No providers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($providers->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50/50">{{ $providers->links() }}</div>
    @endif
</div>
@endsection
