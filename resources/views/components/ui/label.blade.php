@props([
    'value' => null
])

<label {{ $attributes->merge(['class' => 'block text-xs font-semibold text-gray-700 mb-1.5 uppercase tracking-wider']) }}>
    {{ $value ?? $slot }}
</label>
