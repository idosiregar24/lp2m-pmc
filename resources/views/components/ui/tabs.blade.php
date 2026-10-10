{{-- Link tabs: :items="[['label' => 'Fakultas', 'url' => …, 'active' => true, 'count' => 3], …]" --}}
@props(['items' => []])

<nav {{ $attributes->class('flex gap-5 overflow-x-auto border-b border-line') }} aria-label="Tab">
    @foreach ($items as $item)
        <a href="{{ $item['url'] }}"
            @class([
                '-mb-px inline-flex items-center gap-2 whitespace-nowrap border-b-2 px-0.5 pb-2.5 pt-1 text-sm transition',
                'border-brand-600 font-medium text-ink' => $item['active'] ?? false,
                'border-transparent text-muted hover:text-ink' => ! ($item['active'] ?? false),
            ])
            @if ($item['active'] ?? false) aria-current="page" @endif>
            {{ $item['label'] }}
            @isset($item['count'])
                <x-ui.badge tone="neutral">{{ $item['count'] }}</x-ui.badge>
            @endisset
        </a>
    @endforeach
</nav>
