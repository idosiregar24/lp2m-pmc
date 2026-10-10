<x-layouts.app title="Penelitian">
    <x-shared.page-header title="Penelitian" description="Data penelitian dosen Universitas PMC." icon="heroicon-o-beaker">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('researches.export', $filters)" icon="heroicon-o-arrow-down-tray" class="w-full sm:w-auto">Ekspor Excel</x-ui.button>
            @can('create', App\Models\Research::class)
                <x-ui.button :href="route('researches.create')" icon="heroicon-o-plus" class="w-full sm:w-auto">Tambah Penelitian</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        @include('researches._filters')

        @if ($researches->isEmpty())
            @if (array_filter($filters))
                <x-ui.empty-state icon="heroicon-o-beaker" title="Tidak ada data yang cocok dengan filter.">
                    <x-ui.button variant="ghost" :href="route('researches.index')">Reset filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="heroicon-o-beaker" title="Belum ada data penelitian." description="Data yang Anda input atau yang mencantumkan Anda sebagai anggota akan tampil di sini.">
                    @can('create', App\Models\Research::class)
                        <x-ui.button :href="route('researches.create')" icon="heroicon-o-plus">Tambah Penelitian</x-ui.button>
                    @endcan
                </x-ui.empty-state>
            @endif
        @else
            {{-- Phone: cards --}}
            <ul class="divide-y divide-line md:hidden">
                @foreach ($researches as $research)
                    <li class="space-y-2 p-4">
                        <a href="{{ route('researches.show', $research) }}" class="block font-medium text-ink underline-offset-4 hover:underline">{{ $research->title }}</a>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span>{{ $research->year }}</span>
                            <x-ui.badge :tone="$research->status->tone()">{{ $research->status->label() }}</x-ui.badge>
                            <span>{{ $research->scheme->name }}</span>
                        </div>
                        <p class="text-sm text-muted">
                            {{ $research->leadContributor?->displayName() }}{{ $research->contributors_count > 1 ? ', dkk.' : '' }}
                        </p>
                        @include('researches._actions')
                    </li>
                @endforeach
            </ul>

            {{-- Tablet & desktop: table --}}
            <x-ui.table class="hidden md:block">
                <x-slot:head>
                    <tr>
                        <th class="min-w-64">Judul</th>
                        <th>Tahun</th>
                        <th class="min-w-40">Ketua</th>
                        <th class="min-w-48">Skema · Sumber Dana</th>
                        <th class="hidden xl:table-cell">Dokumen</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($researches as $research)
                    <tr>
                        <td>
                            <a href="{{ route('researches.show', $research) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $research->title }}</a>
                        </td>
                        <td class="tabular-nums">{{ $research->year }}</td>
                        <td>
                            {{ $research->leadContributor?->displayName() }}
                            @if ($research->contributors_count > 1)
                                <span class="text-muted">dkk.</span>
                            @endif
                        </td>
                        <td>
                            <span class="block">{{ $research->scheme->name }}</span>
                            <span class="text-xs text-muted">{{ $research->fundingSource->name }}</span>
                        </td>
                        <td class="hidden tabular-nums text-muted xl:table-cell">{{ $research->documents_count }}</td>
                        <td><x-ui.badge :tone="$research->status->tone()">{{ $research->status->label() }}</x-ui.badge></td>
                        <td class="whitespace-nowrap text-right">@include('researches._actions')</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $researches->links() }}
    </x-ui.card>
</x-layouts.app>
