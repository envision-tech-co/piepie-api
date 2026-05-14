@extends('admin.layouts.app')

@section('title', 'Providers')
@section('page_title', 'Service Providers')

@section('content')
@if ($pendingCount > 0)
    <div class="bg-yellow-50 border border-yellow-200 text-yellow-900 px-4 py-3 rounded-lg mb-4 flex justify-between items-center">
        <span>⚠️ {{ $pendingCount }} provider(s) awaiting approval</span>
        <a href="?status=pending" class="text-sm text-indigo-600 hover:underline">Review pending →</a>
    </div>
@endif

<div class="bg-white rounded-lg shadow mb-6">
    <form method="GET" class="p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or phone"
            class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 md:col-span-2">

        <select name="status" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">All Statuses</option>
            @foreach (['pending', 'approved', 'rejected', 'suspended'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
            <a href="{{ route('admin.providers.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 text-sm">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Speciality</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Online</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rating</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jobs</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($providers as $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.providers.show', $p->id) }}" class="text-indigo-600 font-medium hover:underline">
                                {{ $p->name ?? '(no name)' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono">{{ $p->phone }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->service_speciality ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @include('admin.partials.status_badge', ['status' => $p->status])
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($p->is_online)
                                <span class="text-green-600">● Online</span>
                            @else
                                <span class="text-gray-400">○ Offline</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm">⭐ {{ (float) $p->overall_rating }}</td>
                        <td class="px-6 py-4 text-sm">{{ $p->total_jobs }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.providers.show', $p->id) }}" class="text-indigo-600 hover:underline text-sm">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-8 text-center text-gray-500">No providers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t">{{ $providers->links() }}</div>
</div>
@endsection
