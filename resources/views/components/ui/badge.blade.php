@props([
    'tone' => 'neutral', // success|warning|danger|info|accent|brand|neutral
])

@php
    $tones = [
        'success' => 'border-success-line bg-success-soft text-success-ink',
        'warning' => 'border-warning-line bg-warning-soft text-warning-ink',
        'danger' => 'border-danger-line bg-danger-soft text-danger-ink',
        'info' => 'border-info-line bg-info-soft text-info-ink',
        'accent' => 'border-brand-200/80 bg-brand-50 text-brand-700 font-medium',
        'brand' => 'border-brand-200/80 bg-brand-50 text-brand-700 font-medium',
        'neutral' => 'border-line bg-slate-100/70 text-slate-600',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center whitespace-nowrap rounded-md border px-2 py-0.5 text-xs font-medium', $tones[$tone] ?? $tones['neutral']]) }}>
    {{ $slot }}
</span>

