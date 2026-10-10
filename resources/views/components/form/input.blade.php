@props([
    'name',
    'type' => 'text',
    'value' => null,
    'id' => null,
])

@php
    $dot = trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
    $invalid = $errors->has($dot);
    $current = $type === 'password' ? null : old($dot, $value);
@endphp

<input type="{{ $type }}" name="{{ $name }}" id="{{ $id }}"
    @if (! is_null($current)) value="{{ $current }}" @endif
    @if ($invalid) aria-invalid="true" aria-describedby="{{ str_replace('.', '_', $dot) }}_error" @endif
    {{ $attributes->class([
        'block w-full rounded-lg border bg-surface px-3 py-1.5 text-sm text-ink shadow-xs placeholder:text-muted/70',
        'focus:border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-600/10',
        'disabled:bg-canvas disabled:text-muted',
        'border-danger' => $invalid,
        'border-line' => ! $invalid,
    ]) }}>
