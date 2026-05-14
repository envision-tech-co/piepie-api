@extends('admin.layouts.app')

@section('title', 'Customers')
@section('page_title', 'Customers')

@section('content')
<div class="bg-white rounded-lg shadow mb-6">
    <form method="GET" class="p-4 grid grid-cols-1 md:grid-cols-4 gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or phone"
            class="px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 md:col-span-2">

        <select name="is_active" class="px-3 py-2 border rounded-lg text-sm">
            <option value="">All Customers</option>
            <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active only</option>
            <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive only</option>
        </select>

        <div class="flex gap-2">
            <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Filter</button>
            <a href="{{ route('admin.customers.index') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900 text-sm">Reset</a>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Language</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Joined</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($customers as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="text-indigo-600 font-medium hover:underline">
                                {{ $c->name ?? '(no name)' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono">{{ $c->phone }}</td>
                        <td class="px-6 py-4 text-sm">{{ strtoupper($c->language) }}</td>
                        <td class="px-6 py-4">
                            @if ($c->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $c->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.customers.show', $c->id) }}" class="text-indigo-600 hover:underline text-sm mr-3">View</a>
                            <form action="{{ route('admin.customers.toggle', $c->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-yellow-600 hover:underline text-sm">
                                    {{ $c->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-8 text-center text-gray-500">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t">{{ $customers->links() }}</div>
</div>
@endsection
