<x-layouts.app title="Publikasi">
    <x-shared.page-header title="Publikasi" description="Artikel jurnal dan prosiding dosen Universitas PMC." icon="heroicon-o-newspaper">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('publications.export', $filters)" icon="heroicon-o-arrow-down-tray" class="w-full sm:w-auto">Ekspor Excel</x-ui.button>
            @can('create', App\Models\Publication::class)
                <x-ui.button :href="route('publications.create')" icon="heroicon-o-plus" class="w-full sm:w-auto">Tambah Publikasi</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        @include('publications._filters')

        @if ($publications->isEmpty())
            @if (array_filter($filters))
                <x-ui.empty-state icon="heroicon-o-newspaper" title="Tidak ada data yang cocok dengan filter.">
                    <x-ui.button variant="ghost" :href="route('publications.index')">Reset filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="heroicon-o-newspaper" title="Belum ada data publikasi." description="Data yang Anda input atau yang mencantumkan Anda sebagai anggota akan tampil di sini.">
                    @can('create', App\Models\Publication::class)
                        <x-ui.button :href="route('publications.create')" icon="heroicon-o-plus">Tambah Publikasi</x-ui.button>
                    @endcan
                </x-ui.empty-state>
            @endif
        @else
            {{-- Phone: cards --}}
            <ul class="divide-y divide-line md:hidden">
                @foreach ($publications as $publication)
                    <li class="space-y-2 p-4">
                        <a href="{{ route('publications.show', $publication) }}" class="block font-medium text-ink underline-offset-4 hover:underline">{{ $publication->title }}</a>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span>{{ $publication->year }}</span>
                            <x-ui.badge :tone="$publication->status->tone()">{{ $publication->status->label() }}</x-ui.badge>
                            <x-ui.badge :tone="$publication->level->tone()">{{ $publication->level->label() }}</x-ui.badge>
                            <span>{{ $publication->venue_name }}</span>
                        </div>
                        <p class="text-sm text-muted">
                            {{ $publication->leadContributor?->displayName() }}{{ $publication->contributors_count > 1 ? ', dkk.' : '' }}
                        </p>
                        @include('publications._actions')
                    </li>
                @endforeach
            </ul>

            {{-- Tablet & desktop: table --}}
            <x-ui.table class="hidden md:block">
                <x-slot:head>
                    <tr>
                        <th class="min-w-64">Judul</th>
                        <th>Tahun</th>
                        <th class="min-w-40">Penulis Pertama</th>
                        <th class="min-w-48">Jurnal / Prosiding</th>
                        <th>Level</th>
                        <th class="hidden xl:table-cell">Dokumen</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($publications as $publication)
                    <tr>
                        <td>
                            <a href="{{ route('publications.show', $publication) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $publication->title }}</a>
                        </td>
                        <td class="tabular-nums">{{ $publication->year }}</td>
                        <td>
                            {{ $publication->leadContributor?->displayName() }}
                            @if ($publication->contributors_count > 1)
                                <span class="text-muted">dkk.</span>
                            @endif
                        </td>
                        <td>
                            <span class="block">{{ $publication->venue_name }}</span>
                            <span class="text-xs text-muted">{{ $publication->venue_type->label() }}</span>
                        </td>
                        <td><x-ui.badge :tone="$publication->level->tone()">{{ $publication->level->label() }}</x-ui.badge></td>
                        <td class="hidden tabular-nums text-muted xl:table-cell">{{ $publication->documents_count }}</td>
                        <td><x-ui.badge :tone="$publication->status->tone()">{{ $publication->status->label() }}</x-ui.badge></td>
                        <td class="whitespace-nowrap text-right">@include('publications._actions')</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $publications->links() }}
    </x-ui.card>
</x-layouts.app>
