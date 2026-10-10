@props([
    'variant' => 'primary', // primary|secondary|outline|outline-danger|ghost|danger
    'size' => 'md',         // sm|md
    'href' => null,
    'icon' => null,         // heroicon component name, e.g. heroicon-o-plus
    'type' => 'submit',
])

@php
    $variants = [
        'primary' => 'bg-brand-600 text-white shadow-xs hover:bg-brand-700',
        'secondary' => 'border border-line bg-surface text-ink shadow-xs hover:bg-canvas',
        'outline' => 'border border-line bg-surface text-ink shadow-xs hover:bg-canvas',
        'outline-danger' => 'border border-line bg-surface text-danger-ink shadow-xs hover:border-danger-line hover:bg-danger-soft',
        'ghost' => 'text-muted hover:bg-canvas hover:text-ink',
        'danger' => 'bg-danger-ink text-white shadow-xs hover:bg-danger',
    ];
    $sizes = [
        'sm' => 'h-8 gap-1.5 px-2.5 text-xs',
        'md' => 'h-9 gap-2 px-3.5 text-sm',
    ];
    $classes = 'inline-flex items-center justify-center rounded-lg font-medium whitespace-nowrap transition '
        .'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 '
        .'disabled:pointer-events-none disabled:opacity-50 '
        .($variants[$variant] ?? $variants['primary']).' '.($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" class="size-4 shrink-0" aria-hidden="true" />
        @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($icon)
            <x-dynamic-component :component="$icon" class="size-4 shrink-0" aria-hidden="true" />
        @endif
        {{ $slot }}
    </button>
@endif
