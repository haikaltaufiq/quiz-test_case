@props([
    'variant' => 'primary', // primary, secondary, danger
    'type' => 'button'
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium text-sm px-3 py-1.5 rounded-md border transition-colors focus:outline-none focus:ring-1 focus:ring-gray-400 disabled:opacity-50 disabled:pointer-events-none cursor-pointer';
    
    $variants = [
        'primary' => 'bg-gray-900 border-gray-900 text-white hover:bg-gray-800 hover:border-gray-800',
        'secondary' => 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-gray-950',
        'danger' => 'bg-white border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>
