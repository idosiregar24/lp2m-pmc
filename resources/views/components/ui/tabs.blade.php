{{-- Link tabs: :items="[['label' => 'Fakultas', 'url' => …, 'active' => true, 'count' => 3], …]" --}}
@props(['items' => []])

<nav {{ $attributes->class('inline-flex max-w-full items-center gap-1 overflow-x-auto rounded-lg border border-line bg-slate-100/80 p-1 text-xs') }} aria-label="Tab">
    @foreach ($items as $item)
        <a href="{{ $item['url'] }}"
            @class([
                'inline-flex items-center gap-2 whitespace-nowrap rounded-md px-3 py-1.5 text-xs font-medium transition-colors',
                'border border-brand-200/70 bg-surface font-semibold text-brand-700 shadow-xs' => $item['active'] ?? false,
                'border border-transparent text-slate-600 hover:bg-surface/60 hover:text-brand-700' => ! ($item['active'] ?? false),
            ])
            @if ($item['active'] ?? false) aria-current="page" @endif>
            <span>{{ $item['label'] }}</span>
            @isset($item['count'])
                <span @class([
                    'rounded-full px-1.5 py-0.2 text-[10px] font-semibold tabular-nums leading-none',
                    'border border-brand-200/70 bg-brand-100 text-brand-800' => $item['active'] ?? false,
                    'bg-slate-200/80 text-slate-600' => ! ($item['active'] ?? false),
                ])>{{ $item['count'] }}</span>
            @endisset
        </a>
    @endforeach
</nav>

