{{--
    GET filter form. The `search` slot is always visible; the other filters (default
    slot) fold behind a "Filter" toggle on phones. Reset clears the query string.
    :lecturer = the User of a `lecturer` filter (drill-down from Rekap); it has no
    control of its own, so it is kept as a hidden field and shown as a chip.
--}}
@props([
    'action' => null,
    'lecturer' => null,
    'keep' => [],     // query keys owned by the page, not the filters (e.g. Verifikasi tab `type`)
])

@php
    $action ??= url()->current();
    $activeFilters = collect(request()->except(['page', 'q', ...$keep]))->filter(fn ($value) => filled($value))->count();
    $kept = array_filter(request()->only($keep), fn ($value) => is_string($value) && $value !== '');
@endphp

<form method="GET" action="{{ $action }}" x-data="{ open: @js($activeFilters > 0) }"
    {{ $attributes->class('space-y-3 border-b border-line p-4') }}>
    @if ($lecturer)
        <input type="hidden" name="lecturer" value="{{ $lecturer->id }}">
        <p class="flex flex-wrap items-center gap-2 text-sm text-muted">
            Data yang mencantumkan
            <span class="inline-flex items-center gap-1 rounded-full border border-line bg-canvas px-2.5 py-0.5 font-medium text-ink">
                {{ $lecturer->name }}
                <a href="{{ request()->fullUrlWithoutQuery(['lecturer', 'page']) }}" class="rounded-full hover:text-muted" aria-label="Hapus filter dosen">
                    <x-heroicon-o-x-mark class="size-3.5" />
                </a>
            </span>
        </p>
    @endif
    @isset($search)
        <div class="flex gap-2">
            <div class="min-w-0 flex-1">{{ $search }}</div>
            @if ($slot->isNotEmpty())
                <button type="button" class="inline-flex h-10 items-center gap-1.5 rounded-lg border border-line bg-surface px-3 text-sm text-ink sm:hidden"
                    @click="open = ! open" :aria-expanded="open">
                    <x-heroicon-o-adjustments-horizontal class="size-4" />
                    Filter
                    @if ($activeFilters)
                        <span class="rounded-full bg-brand-600 px-1.5 text-xs text-white">{{ $activeFilters }}</span>
                    @endif
                </button>
            @endif
        </div>
    @endisset

    @if ($slot->isNotEmpty())
        <div class="grid gap-3 max-sm:hidden sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6" x-bind:class="{ 'max-sm:hidden': ! open }">
    @else
        <div class="flex gap-2">
    @endif
        {{ $slot }}
        <div class="flex items-end gap-2">
            <x-ui.button icon="heroicon-o-funnel" class="flex-1 sm:flex-none">Terapkan</x-ui.button>
            @if (collect(request()->except(['page', ...$keep]))->filter(fn ($value) => filled($value))->isNotEmpty())
                <x-ui.button variant="ghost" :href="$action.($kept ? '?'.http_build_query($kept) : '')">Reset</x-ui.button>
            @endif
        </div>
    </div>
</form>
