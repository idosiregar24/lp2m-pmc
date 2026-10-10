@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
    'href' => null,
])

@php($tag = $href ? 'a' : 'div')

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class(['flex flex-col gap-3 rounded-2xl border border-line bg-surface p-4 sm:flex-row sm:items-start sm:gap-4 sm:p-5', 'transition hover:border-brand-200' => $href]) }}>
    @if ($icon)
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg border border-brand-100 bg-brand-50 text-brand-600">
            <x-dynamic-component :component="$icon" class="size-5" aria-hidden="true" />
        </span>
    @endif
    <div class="min-w-0">
        <p class="text-sm text-muted">{{ $label }}</p>
        <p class="mt-1 text-2xl font-semibold text-ink tabular-nums">{{ $value }}</p>
        @if ($hint)
            <p class="mt-0.5 text-xs text-muted">{{ $hint }}</p>
        @endif
    </div>
</{{ $tag }}>
