<x-layouts.app title="HKI">
    <x-shared.page-header title="HKI" description="Hak Kekayaan Intelektual dosen Universitas PMC." icon="heroicon-o-light-bulb">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('intellectual-properties.export', $filters)" icon="heroicon-o-arrow-down-tray" class="w-full sm:w-auto">Ekspor Excel</x-ui.button>
            @can('create', App\Models\IntellectualProperty::class)
                <x-ui.button :href="route('intellectual-properties.create')" icon="heroicon-o-plus" class="w-full sm:w-auto">Tambah HKI</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        @include('intellectual-properties._filters')

        @if ($intellectualProperties->isEmpty())
            @if (array_filter($filters))
                <x-ui.empty-state icon="heroicon-o-light-bulb" title="Tidak ada data yang cocok dengan filter.">
                    <x-ui.button variant="ghost" :href="route('intellectual-properties.index')">Reset filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="heroicon-o-light-bulb" title="Belum ada data HKI." description="Data yang Anda input atau yang mencantumkan Anda sebagai anggota akan tampil di sini.">
                    @can('create', App\Models\IntellectualProperty::class)
                        <x-ui.button :href="route('intellectual-properties.create')" icon="heroicon-o-plus">Tambah HKI</x-ui.button>
                    @endcan
                </x-ui.empty-state>
            @endif
        @else
            {{-- Phone: cards --}}
            <ul class="divide-y divide-line md:hidden">
                @foreach ($intellectualProperties as $intellectualProperty)
                    <li class="space-y-2 p-4">
                        <a href="{{ route('intellectual-properties.show', $intellectualProperty) }}" class="block font-medium text-ink underline-offset-4 hover:underline">{{ $intellectualProperty->title }}</a>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span>{{ $intellectualProperty->year }}</span>
                            <x-ui.badge :tone="$intellectualProperty->status->tone()">{{ $intellectualProperty->status->label() }}</x-ui.badge>
                            <x-ui.badge>{{ $intellectualProperty->type->label() }}</x-ui.badge>
                        </div>
                        <p class="text-sm text-muted">
                            {{ $intellectualProperty->leadContributor?->displayName() }}{{ $intellectualProperty->contributors_count > 1 ? ', dkk.' : '' }}
                        </p>
                        @include('intellectual-properties._actions')
                    </li>
                @endforeach
            </ul>

            {{-- Tablet & desktop: table --}}
            <x-ui.table class="hidden md:block">
                <x-slot:head>
                    <tr>
                        <th class="min-w-64">Judul</th>
                        <th>Tahun</th>
                        <th class="min-w-40">Pemilik</th>
                        <th class="min-w-40">Jenis KI</th>
                        <th class="hidden xl:table-cell">Dokumen</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($intellectualProperties as $intellectualProperty)
                    <tr>
                        <td>
                            <a href="{{ route('intellectual-properties.show', $intellectualProperty) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $intellectualProperty->title }}</a>
                        </td>
                        <td class="tabular-nums">{{ $intellectualProperty->year }}</td>
                        <td>
                            {{ $intellectualProperty->leadContributor?->displayName() }}
                            @if ($intellectualProperty->contributors_count > 1)
                                <span class="text-muted">dkk.</span>
                            @endif
                        </td>
                        <td><x-ui.badge>{{ $intellectualProperty->type->label() }}</x-ui.badge></td>
                        <td class="hidden tabular-nums text-muted xl:table-cell">{{ $intellectualProperty->documents_count }}</td>
                        <td><x-ui.badge :tone="$intellectualProperty->status->tone()">{{ $intellectualProperty->status->label() }}</x-ui.badge></td>
                        <td class="whitespace-nowrap text-right">@include('intellectual-properties._actions')</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $intellectualProperties->links() }}
    </x-ui.card>
</x-layouts.app>
