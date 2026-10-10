<x-layouts.app title="Pengabdian">
    <x-shared.page-header title="Pengabdian" description="Data pengabdian kepada masyarakat dosen Universitas PMC." icon="heroicon-o-user-group">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('community-services.export', $filters)" icon="heroicon-o-arrow-down-tray" class="w-full sm:w-auto">Ekspor Excel</x-ui.button>
            @can('create', App\Models\CommunityService::class)
                <x-ui.button :href="route('community-services.create')" icon="heroicon-o-plus" class="w-full sm:w-auto">Tambah Pengabdian</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        @include('community-services._filters')

        @if ($communityServices->isEmpty())
            @if (array_filter($filters))
                <x-ui.empty-state icon="heroicon-o-user-group" title="Tidak ada data yang cocok dengan filter.">
                    <x-ui.button variant="ghost" :href="route('community-services.index')">Reset filter</x-ui.button>
                </x-ui.empty-state>
            @else
                <x-ui.empty-state icon="heroicon-o-user-group" title="Belum ada data pengabdian." description="Data yang Anda input atau yang mencantumkan Anda sebagai anggota akan tampil di sini.">
                    @can('create', App\Models\CommunityService::class)
                        <x-ui.button :href="route('community-services.create')" icon="heroicon-o-plus">Tambah Pengabdian</x-ui.button>
                    @endcan
                </x-ui.empty-state>
            @endif
        @else
            {{-- Phone: cards --}}
            <ul class="divide-y divide-line md:hidden">
                @foreach ($communityServices as $communityService)
                    <li class="space-y-2 p-4">
                        <a href="{{ route('community-services.show', $communityService) }}" class="block font-medium text-ink underline-offset-4 hover:underline">{{ $communityService->title }}</a>
                        <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                            <span>{{ $communityService->year }}</span>
                            <x-ui.badge :tone="$communityService->status->tone()">{{ $communityService->status->label() }}</x-ui.badge>
                            <span>{{ $communityService->partner }}</span>
                        </div>
                        <p class="text-sm text-muted">
                            {{ $communityService->leadContributor?->displayName() }}{{ $communityService->contributors_count > 1 ? ', dkk.' : '' }}
                        </p>
                        @include('community-services._actions')
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
                        <th class="min-w-48">Mitra · Skema</th>
                        <th class="hidden xl:table-cell">Dokumen</th>
                        <th>Status</th>
                        <th class="text-right"><span class="sr-only">Aksi</span></th>
                    </tr>
                </x-slot:head>
                @foreach ($communityServices as $communityService)
                    <tr>
                        <td>
                            <a href="{{ route('community-services.show', $communityService) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $communityService->title }}</a>
                        </td>
                        <td class="tabular-nums">{{ $communityService->year }}</td>
                        <td>
                            {{ $communityService->leadContributor?->displayName() }}
                            @if ($communityService->contributors_count > 1)
                                <span class="text-muted">dkk.</span>
                            @endif
                        </td>
                        <td>
                            <span class="block">{{ $communityService->partner }}</span>
                            <span class="text-xs text-muted">{{ $communityService->scheme->name }}</span>
                        </td>
                        <td class="hidden tabular-nums text-muted xl:table-cell">{{ $communityService->documents_count }}</td>
                        <td><x-ui.badge :tone="$communityService->status->tone()">{{ $communityService->status->label() }}</x-ui.badge></td>
                        <td class="whitespace-nowrap text-right">@include('community-services._actions')</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $communityServices->links() }}
    </x-ui.card>
</x-layouts.app>
