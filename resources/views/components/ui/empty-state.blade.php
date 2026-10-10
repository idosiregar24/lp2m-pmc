@props([
    'title',
    'description' => null,
    'icon' => 'heroicon-o-inbox',
])

<div {{ $attributes->class('flex flex-col items-center px-6 py-12 text-center') }}>
    <span class="flex size-12 items-center justify-center rounded-xl border border-line bg-canvas text-muted">
        <x-dynamic-component :component="$icon" class="size-6" aria-hidden="true" />
    </span>
    <p class="mt-4 text-sm font-semibold text-ink">{{ $title }}</p>
    @if ($description)
        <p class="mt-1 max-w-sm text-sm text-muted">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-4 flex flex-wrap justify-center gap-2">{{ $slot }}</div>
    @endif
</div>
