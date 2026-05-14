@php
    $colors = [
        'pending' => 'bg-gray-100 text-gray-700 ring-gray-200',
        'searching' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        'accepted' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'on_the_way' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'arrived' => 'bg-purple-50 text-purple-700 ring-purple-200',
        'in_progress' => 'bg-orange-50 text-orange-700 ring-orange-200',
        'completed' => 'bg-green-50 text-green-700 ring-green-200',
        'cancelled' => 'bg-red-50 text-red-700 ring-red-200',
        // Provider statuses
        'approved' => 'bg-green-50 text-green-700 ring-green-200',
        'rejected' => 'bg-red-50 text-red-700 ring-red-200',
        'suspended' => 'bg-gray-100 text-gray-700 ring-gray-200',
        // Payment
        'paid' => 'bg-green-50 text-green-700 ring-green-200',
        'refunded' => 'bg-gray-100 text-gray-700 ring-gray-200',
        // Offer statuses
        'declined' => 'bg-red-50 text-red-700 ring-red-200',
        'expired' => 'bg-gray-100 text-gray-500 ring-gray-200',
    ];
    $class = $colors[$status] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
    $label = $label ?? ucwords(str_replace('_', ' ', $status));
@endphp
<span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full ring-1 ring-inset {{ $class }}">
    {{ $label }}
</span>
