{{-- Boolean checkbox; a hidden "0" keeps the value when unchecked. --}}
@props([
    'name',
    'label',
    'checked' => false,
    'id' => null,
])

@php
    $id ??= str_replace(['[', ']', '.'], ['_', '', '_'], $name);
    $isChecked = (bool) old($name, $checked);
@endphp

<label for="{{ $id }}" {{ $attributes->class('inline-flex cursor-pointer items-center gap-2 text-sm text-ink') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" id="{{ $id }}" value="1" @checked($isChecked)
        class="size-4 rounded border-line accent-brand-600 focus:ring-2 focus:ring-brand-600/20">
    {{ $label }}
</label>
