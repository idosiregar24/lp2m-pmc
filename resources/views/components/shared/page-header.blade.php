{{-- Large page title (modern, no icon tile); `icon` is kept for callers but no longer drawn. --}}
@props([
    'title',
    'description' => null,
    'icon' => null,
])

<div {{ $attributes->class('flex flex-wrap items-end justify-between gap-4') }}>
    <div class="min-w-0">
        <h1 class="text-2xl font-semibold tracking-tight text-ink sm:text-3xl">{{ $title }}</h1>
        @if ($description)
            <p class="mt-1.5 text-sm text-muted">{{ $description }}</p>
        @endif
        @isset($meta)
            <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-muted">{{ $meta }}</div>
        @endisset
    </div>
    @isset($actions)
        <div class="flex w-full flex-wrap items-center gap-2 sm:w-auto">{{ $actions }}</div>
    @endisset
</div>
