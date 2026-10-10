@props([
    'label',
    'wide' => false,
])

<div @class(['min-w-0', 'sm:col-span-2' => $wide])>
    <dt class="text-sm text-muted">{{ $label }}</dt>
    <dd {{ $attributes->class('mt-1 text-sm text-ink') }}>
        @if ($slot->isEmpty())
            <span class="text-muted">—</span>
        @else
            {{ $slot }}
        @endif
    </dd>
</div>
