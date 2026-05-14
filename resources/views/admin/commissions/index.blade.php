@extends('admin.layouts.app')

@section('title', 'Commissions')
@section('page_title', 'Commission Settings')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-24">
            <h3 class="text-lg font-semibold text-gray-800 mb-1">Set Commission Rate</h3>
            <p class="text-sm text-gray-500 mb-5">Set a global default or override per category.</p>

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
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Category</label>
                    <select name="service_category_id" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">🌐 Global Default (all categories)</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Commission Rate (%)</label>
                    <div class="relative">
                        <input type="number" name="rate" step="0.01" min="0" max="100" value="{{ old('rate', 20) }}" required
                            class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-transparent pr-10">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white py-2.5 rounded-lg text-sm font-medium transition shadow-sm">
                    Save Commission
                </button>
            </form>

            <div class="mt-5 pt-5 border-t">
                <div class="text-xs text-gray-500 leading-relaxed">
                    <strong>How it works:</strong> When a booking is created, the system checks for a category-specific rate first. If none exists, it uses the global default. If no settings exist at all, it falls back to <code class="bg-gray-100 px-1 rounded">{{ config('pippip.default_commission') }}%</code> from config.
                </div>
            </div>
        </div>
    </div>

    {{-- List --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Current Settings</h3>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($commissions as $c)
                    <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0
                                {{ $c->service_category_id === null ? 'bg-primary-100 text-primary-700' : 'bg-gray-100 text-gray-600' }}">
                                @if ($c->service_category_id === null)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ $c->service_category_id === null ? 'Global Default' : ($c->serviceCategory->name_en ?? 'Unknown') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    Updated {{ $c->updated_at->diffForHumans() }}
                                    @if ($c->creator)
                                        by {{ $c->creator->name }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <div class="text-xl font-bold text-gray-900">{{ number_format($c->rate, 1) }}<span class="text-sm text-gray-500">%</span></div>
                            </div>

                            @if ($c->service_category_id !== null)
                                <form action="{{ route('admin.commissions.destroy', $c->id) }}" method="POST"
                                    x-data @submit.prevent="if(confirm('Remove this override? The category will use the global rate.')) $el.submit()">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Remove override">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <div class="w-10"></div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-12 text-center text-gray-400">
                        <p class="font-medium">No commission settings yet</p>
                        <p class="text-sm mt-1">Use the form to set your first commission rate.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
