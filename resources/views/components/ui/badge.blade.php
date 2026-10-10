@props([
    'tone' => 'neutral', // success|warning|danger|info|accent|neutral
])

@php
    $tones = [
        'success' => 'border-success-line bg-success-soft text-success-ink',
        'warning' => 'border-warning-line bg-warning-soft text-warning-ink',
        'danger' => 'border-danger-line bg-danger-soft text-danger-ink',
        'info' => 'border-info-line bg-info-soft text-info-ink',
        'accent' => 'border-accent-line bg-accent-soft text-accent-ink',
        'neutral' => 'border-line bg-surface text-muted',
    ];
@endphp

<span {{ $attributes->class(['inline-flex items-center whitespace-nowrap rounded-full border px-2 py-0.5 text-xs font-medium', $tones[$tone] ?? $tones['neutral']]) }}>
    {{ $slot }}
</span>
