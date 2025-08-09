@props([
    'type' => 'info', // success, error, warning, info
    'message' => '',
    'timeout' => 3000,
])

@php
    $colors = [
        'success' => 'bg-green-600 text-white border-green-500',
        'error' => 'bg-red-600 text-white border-red-500',
        'warning' => 'bg-yellow-600 text-white border-yellow-500',
        'info' => 'bg-blue-600 text-white border-blue-500',
    ];
    $icons = [
        'success' => 'M4.5 12.75l6 6 9-13.5',
        'error' => 'M6 18L18 6M6 6l12 12',
        'warning' => 'M12 9v3m0 4h.01M10.34 2.88l-8.49 14.72A1.75 1.75 0 003.34 20h17.32a1.75 1.75 0 001.49-2.4L13.66 2.88a1.75 1.75 0 00-3.32 0z',
        'info' => 'M13 16h-1v-4h-1m1-4h.01',
    ];
    $color = $colors[$type] ?? $colors['info'];
    $iconPath = $icons[$type] ?? $icons['info'];
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:enter="transform ease-out duration-500"
    x-transition:enter-start="translate-x-8 opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    x-transition:leave="transform ease-in duration-500"
    x-transition:leave-start="translate-x-0 opacity-100"
    x-transition:leave-end="translate-x-8 opacity-0"
    x-init="setTimeout(() => show = false, {{ (int) $timeout }})"
    class="pointer-events-auto self-end w-auto max-w-sm overflow-hidden rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 border {{ $color }}"
>
    <div class="p-4 flex items-start gap-3">
        <div class="shrink-0">
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $iconPath }}" />
            </svg>
        </div>
        <div class="flex-1 text-sm">
            {!! $message !!}
        </div>
    </div>
</div>
