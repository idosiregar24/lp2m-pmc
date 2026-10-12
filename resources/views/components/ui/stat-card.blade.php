@props([
    'label',
    'value',
    'hint' => null,
    'icon' => null,
    'href' => null,
])

@php($tag = $href ? 'a' : 'div')

<{{ $tag }} @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class(['flex flex-col gap-3 rounded-xl border border-line bg-surface p-4 shadow-xs transition hover:border-brand-200/80 sm:flex-row sm:items-start sm:gap-3.5', 'cursor-pointer hover:bg-brand-50/20' => $href]) }}>
    @if ($icon)
        <span class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-brand-200/70 bg-brand-50 text-brand-700 shadow-2xs">
            <x-dynamic-component :component="$icon" class="size-4.5" aria-hidden="true" />
        </span>
    @endif
    <div class="min-w-0">
        <p class="text-xs font-medium text-muted">{{ $label }}</p>
        <p class="mt-1 text-2xl font-bold tracking-tight text-ink tabular-nums">{{ $value }}</p>
        @if ($hint)
            <p class="mt-0.5 text-[11px] text-muted">{{ $hint }}</p>
        @endif
    </div>
</{{ $tag }}>
