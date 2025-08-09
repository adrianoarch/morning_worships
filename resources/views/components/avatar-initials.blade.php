@props([
    'name' => '',
    'size' => '16', // tailwind size like 8, 10, 12, 16
    'class' => '',
])
@php
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->map(fn($p) => mb_substr($p, 0, 1))
        ->take(2)
        ->implode('');
    $sizeClass = 'h-' . $size . ' w-' . $size;
@endphp
<div {{ $attributes->merge(['class' => "$sizeClass rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold select-none shrink-0 overflow-hidden aspect-square $class"]) }}>
    <span class="leading-none">{{ $initials ?: '?' }}</span>
</div>
