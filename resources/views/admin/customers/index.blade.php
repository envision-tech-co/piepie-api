@extends('admin.layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers')

@section('content')
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
            <select name="is_active" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500">
                <option value="">All</option>
                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>

        <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">Filter</button>
        <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 text-gray-500 hover:text-gray-700 text-sm">Reset</a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl shadow-sm border">
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50/80">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Phone</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Language</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Active</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($customers as $c)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="flex items-center gap-3 group">
                                <div class="w-9 h-9 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center font-semibold text-sm flex-shrink-0">
                                    {{ strtoupper(substr($c->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900 group-hover:text-primary-600 transition">{{ $c->name ?? '(no name)' }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $c->phone }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex px-2 py-0.5 text-xs font-semibold rounded bg-gray-100 text-gray-700">{{ strtoupper($c->language) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.customers.toggle', $c->id) }}" method="POST" class="inline-flex justify-center">
                                @csrf
                                <label class="toggle-switch" title="{{ $c->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                    <input type="checkbox" {{ $c->is_active ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </label>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $c->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.customers.show', $c->id) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 rounded-lg transition">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">No customers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($customers->hasPages())
        <div class="px-6 py-4 border-t bg-gray-50/50">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
