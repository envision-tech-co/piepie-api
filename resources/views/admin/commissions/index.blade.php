@extends('admin.layouts.app')

@section('title', 'Commissions')
@section('page_title', 'Commission Settings')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Set/Update commission --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Set Commission Rate</h3>

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-800 p-3 rounded-lg mb-4 text-sm">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.commissions.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Category</label>
                    <select name="service_category_id" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Global Default (applies to all)</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name_en }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Leave as "Global Default" to set the fallback rate.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Commission Rate (%)</label>
                    <input type="number" name="rate" step="0.01" min="0" max="100" value="{{ old('rate', 20) }}" required
                        class="w-full px-3 py-2 border rounded-lg">
                </div>

                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg">
                    Save Commission
                </button>
            </form>
        </div>
    </div>

    {{-- List --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold">Current Commission Settings</h3>
            </div>

            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Scope</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Set by</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Updated</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($commissions as $c)
                        <tr>
                            <td class="px-6 py-4">
                                @if ($c->service_category_id === null)
                                    <span class="font-semibold text-indigo-600">Global Default</span>
                                @else
                                    {{ $c->serviceCategory->name_en ?? 'Unknown' }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-lg font-semibold">{{ number_format($c->rate, 2) }}%</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $c->creator->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $c->updated_at->diffForHumans() }}</td>
                            <td class="px-6 py-4 text-right">
                                @if ($c->service_category_id !== null)
                                    <form action="{{ route('admin.commissions.destroy', $c->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Remove this category-specific override? It will fall back to the global rate.');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 hover:underline text-sm">Remove override</button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">No commission settings yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 text-xs text-gray-500">
            💡 <strong>How it works:</strong> When a booking is created, the system looks for a category-specific override first. If none exists, it uses the global default. If no settings exist, it falls back to the config value ({{ config('pippip.default_commission') }}%).
        </div>
    </div>
</div>
@endsection
