@extends('admin.layouts.app')

@section('title', 'Service Categories')
@section('page_title', 'Service Categories')

@section('content')
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-800">All Categories</h3>
        <a href="{{ route('admin.services.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm px-4 py-2 rounded-lg transition">
            + New Category
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name (EN / AR / KU)</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Icon</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Base Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Active</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($categories as $cat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $cat->sort_order }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $cat->name_en }}</div>
                            <div class="text-xs text-gray-500">{{ $cat->name_ar }} / {{ $cat->name_ku }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-700">{{ $cat->icon }}</td>
                        <td class="px-6 py-4 text-sm">{{ number_format($cat->base_price) }} {{ config('pippip.currency') }}</td>
                        <td class="px-6 py-4">
                            @if ($cat->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <form action="{{ route('admin.services.toggle', $cat->id) }}" method="POST" class="inline">
                                @csrf
                                <button class="text-yellow-600 hover:underline text-sm">Toggle</button>
                            </form>
                            <a href="{{ route('admin.services.edit', $cat->id) }}" class="text-indigo-600 hover:underline text-sm">Edit</a>
                            <form action="{{ route('admin.services.destroy', $cat->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Delete this category? This cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">No categories yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t">
        {{ $categories->links() }}
    </div>
</div>
@endsection
