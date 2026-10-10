{{-- Label + control (slot) + hint + error for one field. --}}
@props([
    'name',
    'label',
    'required' => false,
    'hint' => null,
    'id' => null,
])

@php($id ??= str_replace(['[]', '[', ']', '.'], ['', '_', '', '_'], $name))

<div {{ $attributes->class('space-y-1.5') }}>
    <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
    {{ $slot }}
    @if ($hint)
        <p class="text-xs text-muted">{{ $hint }}</p>
    @endif
    <x-form.error :name="$name" />
</div>
