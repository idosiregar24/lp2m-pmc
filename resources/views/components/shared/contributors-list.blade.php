@props([
    'contributors',
    'leadLabel' => 'Ketua',
])

<ol {{ $attributes->class('divide-y divide-line') }}>
    @foreach ($contributors as $contributor)
        <li class="flex items-start gap-3 py-3 first:pt-0 last:pb-0">
            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-canvas text-xs font-semibold text-muted ring-1 ring-line">{{ $contributor->sort_order }}</span>
            <div class="min-w-0 flex-1">
                <p class="flex flex-wrap items-center gap-2 text-sm font-medium text-ink">
                    {{ $contributor->displayName() }}
                    @if ($contributor->isLead())
                        <x-ui.badge tone="warning">{{ $leadLabel }}</x-ui.badge>
                    @endif
                    @if ($contributor->isExternal())
                        <x-ui.badge>Luar PMC</x-ui.badge>
                    @endif
                </p>
                <p class="text-xs text-muted">
                    @if ($contributor->isExternal())
                        {{ $contributor->affiliation ?: '—' }}
                    @else
                        {{ $contributor->user->studyProgram?->displayName() ?? 'Dosen Universitas PMC' }}
                        @if ($contributor->user->studyProgram?->faculty)
                            · {{ $contributor->user->studyProgram->faculty->name }}
                        @endif
                    @endif
                </p>
            </div>
        </li>
    @endforeach
</ol>
