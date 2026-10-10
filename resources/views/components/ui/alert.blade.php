@props([
    'tone' => 'info', // success|warning|danger|info
    'title' => null,
])

@php
    $tones = [
        'success' => ['border-success-line bg-success-soft text-success-ink', 'heroicon-o-check-circle'],
        'warning' => ['border-warning-line bg-warning-soft text-warning-ink', 'heroicon-o-exclamation-triangle'],
        'danger' => ['border-danger-line bg-danger-soft text-danger-ink', 'heroicon-o-x-circle'],
        'info' => ['border-info-line bg-info-soft text-info-ink', 'heroicon-o-information-circle'],
    ];
    [$classes, $icon] = $tones[$tone] ?? $tones['info'];
@endphp

<div role="{{ $tone === 'danger' ? 'alert' : 'status' }}" {{ $attributes->class(['flex gap-3 rounded-xl border px-4 py-3 text-sm', $classes]) }}>
    <x-dynamic-component :component="$icon" class="mt-0.5 size-5 shrink-0" aria-hidden="true" />
    <div class="min-w-0 space-y-1">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>
</div>
