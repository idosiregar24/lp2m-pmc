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
        'secondary' => 'border border-line bg-surface text-ink shadow-xs hover:border-brand-200/80 hover:bg-brand-50/50 hover:text-brand-700',
        'outline' => 'border border-line bg-surface text-ink shadow-xs hover:border-brand-200/80 hover:bg-brand-50/50 hover:text-brand-700',
        'soft' => 'border border-brand-200/80 bg-brand-50/70 text-brand-700 shadow-2xs hover:bg-brand-100 hover:border-brand-300',
        'outline-danger' => 'border border-line bg-surface text-danger-ink shadow-xs hover:border-danger-line hover:bg-danger-soft',
        'ghost' => 'text-muted hover:bg-brand-50/60 hover:text-brand-700',
        'danger' => 'bg-danger-ink text-white shadow-xs hover:bg-danger',
    ];
    $sizes = [
        'sm' => 'h-8 gap-1.5 px-2.5 text-xs',
        'md' => 'h-9 gap-2 px-3.5 text-sm',
    ];
    $classes = 'inline-flex items-center justify-center rounded-lg font-medium whitespace-nowrap transition-all '
        .'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2 '
        .'active:scale-[0.99] disabled:pointer-events-none disabled:opacity-50 '
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
