@php
    $colors = [
        'pending' => 'bg-gray-100 text-gray-800',
        'searching' => 'bg-yellow-100 text-yellow-800',
        'accepted' => 'bg-blue-100 text-blue-800',
        'on_the_way' => 'bg-indigo-100 text-indigo-800',
        'arrived' => 'bg-purple-100 text-purple-800',
        'in_progress' => 'bg-orange-100 text-orange-800',
        'completed' => 'bg-green-100 text-green-800',
        'cancelled' => 'bg-red-100 text-red-800',
        // Provider statuses
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        'suspended' => 'bg-gray-200 text-gray-800',
        // Payment
        'paid' => 'bg-green-100 text-green-800',
        'refunded' => 'bg-gray-100 text-gray-800',
    ];
    $class = $colors[$status] ?? 'bg-gray-100 text-gray-800';
    $label = $label ?? ucwords(str_replace('_', ' ', $status));
@endphp
<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $class }}">
    {{ $label }}
</span>
