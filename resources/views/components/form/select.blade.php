{{--
    :options = [value => label] or grouped [groupLabel => [value => label]].
    Never query inside this component — options come from the controller.
--}}
@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => null,
    'id' => null,
])

@php
    $dot = trim(str_replace(['[]', '[', ']'], ['', '.', ''], $name), '.');
    $id ??= str_replace('.', '_', $dot);
    $invalid = $errors->has($dot);
    $selected = old($dot, $value instanceof \BackedEnum ? $value->value : $value);
    $selected = is_null($selected) ? '' : (string) $selected;
@endphp

<select name="{{ $name }}" id="{{ $id }}"
    @if ($invalid) aria-invalid="true" aria-describedby="{{ str_replace('.', '_', $dot) }}_error" @endif
    {{ $attributes->class([
        'block w-full rounded-lg border bg-surface px-3 py-1.5 text-sm text-ink shadow-xs',
        'focus:border-brand-200 focus:outline-none focus:ring-4 focus:ring-brand-600/10',
        'border-danger' => $invalid,
        'border-line' => ! $invalid,
    ]) }}>
    @if (! is_null($placeholder))
        <option value="">{{ $placeholder }}</option>
    @endif
    @foreach ($options as $optionValue => $optionLabel)
        @if (is_array($optionLabel))
            <optgroup label="{{ $optionValue }}">
                @foreach ($optionLabel as $groupValue => $groupLabel)
                    <option value="{{ $groupValue }}" @selected($selected === (string) $groupValue)>{{ $groupLabel }}</option>
                @endforeach
            </optgroup>
        @else
            <option value="{{ $optionValue }}" @selected($selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endif
    @endforeach
</select>
