@props([
    'disabled' => false,
    'type' => 'text'
])

<input {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge([
    'type' => $type,
    'class' => 'block w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-md px-3 py-2 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-gray-900 focus:border-gray-900 disabled:bg-gray-50 disabled:text-gray-500'
]) }}>
