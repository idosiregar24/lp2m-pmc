@props(['name'])

@php($dot = trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.'))

@error($dot)
    <p id="{{ str_replace('.', '_', $dot) }}_error" {{ $attributes->class('mt-1 text-xs text-danger-ink') }}>{{ $message }}</p>
@enderror
