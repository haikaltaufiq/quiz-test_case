@props([
    'variant' => 'gray' // gray, green, red, yellow, blue
])

@php
    $baseClasses = 'inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium border';
    
    $variants = [
        'gray' => 'bg-gray-50 border-gray-200 text-gray-700',
        'green' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
        'red' => 'bg-red-50 border-red-200 text-red-700',
        'yellow' => 'bg-amber-50 border-amber-200 text-amber-800',
        'blue' => 'bg-blue-50 border-blue-200 text-blue-700',
    ];

    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['gray']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
