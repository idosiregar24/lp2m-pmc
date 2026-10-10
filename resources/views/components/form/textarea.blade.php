@props([
    'name',
    'value' => null,
    'id' => null,
    'rows' => 3,
])

@php
    $dot = trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
    $invalid = $errors->has($dot);
@endphp

<textarea name="{{ $name }}" id="{{ $id }}" rows="{{ $rows }}"
    @if ($invalid) aria-invalid="true" aria-describedby="{{ str_replace('.', '_', $dot) }}_error" @endif
    {{ $attributes->class([
        'block w-full rounded-lg border bg-surface px-3 py-2 text-sm text-ink shadow-xs placeholder:text-muted/70',
        'focus:border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-600/10',
        'border-danger' => $invalid,
        'border-line' => ! $invalid,
    ]) }}>{{ old($dot, $value) }}</textarea>
