@props([
    'name',
    'id' => null,
    'multiple' => false,
])

@php
    $dot = trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
    $invalid = $errors->has($dot) || $errors->has($dot.'.*');
@endphp

<input type="file" name="{{ $name }}" id="{{ $id }}" @if ($multiple) multiple @endif
    {{ $attributes->class([
        'block w-full cursor-pointer rounded-lg border bg-surface text-sm text-muted shadow-xs',
        'file:mr-3 file:cursor-pointer file:border-0 file:bg-canvas file:px-4 file:py-2 file:text-sm file:font-medium file:text-ink hover:file:bg-line',
        'focus:outline-none focus:ring-4 focus:ring-brand-600/10',
        'border-danger' => $invalid,
        'border-line' => ! $invalid,
    ]) }}>
