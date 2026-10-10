{{--
    Dialog controlled by an Alpine boolean from the surrounding x-data:
    <div x-data="{ open: false }"> … <x-ui.modal show="open" title="…">…</x-ui.modal></div>
--}}
@props([
    'show' => 'open',
    'title',
    'description' => null,
])

<template x-teleport="body">
    <div x-show="{{ $show }}" x-cloak
        class="fixed inset-0 z-50 flex items-end justify-center p-0 sm:items-center sm:p-4"
        @keydown.escape.window="{{ $show }} = false">
        <div x-show="{{ $show }}" x-transition.opacity class="absolute inset-0 bg-ink/50" @click="{{ $show }} = false" aria-hidden="true"></div>

        <div x-show="{{ $show }}" x-transition x-trap.noscroll="{{ $show }}"
            role="dialog" aria-modal="true"
            {{ $attributes->class('relative w-full max-w-lg rounded-t-2xl bg-surface shadow-xl sm:rounded-xl') }}>
            <div class="flex items-start justify-between gap-4 border-b border-line px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-ink">{{ $title }}</h2>
                    @if ($description)
                        <p class="mt-0.5 text-sm text-muted">{{ $description }}</p>
                    @endif
                </div>
                <button type="button" class="rounded-md p-1 text-muted hover:bg-canvas hover:text-ink" @click="{{ $show }} = false" aria-label="Tutup">
                    <x-heroicon-o-x-mark class="size-5" />
                </button>
            </div>
            {{ $slot }}
        </div>
    </div>
</template>
