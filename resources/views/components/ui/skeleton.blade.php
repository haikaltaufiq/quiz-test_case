@props([
    'lines' => 3
])

<div {{ $attributes->merge(['class' => 'border border-gray-200 rounded-md p-4 bg-white animate-pulse space-y-3']) }}>
    <div class="h-4 bg-gray-200 rounded w-1/3"></div>
    <div class="space-y-2">
        @for($i = 0; $i < $lines; $i++)
            <div class="h-3 bg-gray-200 rounded {{ $i == $lines-1 ? 'w-2/3' : 'w-full' }}"></div>
        @endfor
    </div>
</div>
