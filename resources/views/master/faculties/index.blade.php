<x-layouts.app title="Fakultas" :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Fakultas']]">
    @include('master._header', ['createRoute' => 'master.faculties.create', 'createLabel' => 'Tambah Fakultas'])

    <x-ui.card flush>
        <x-shared.filter-bar :action="route('master.faculties.index')">
            <x-slot:search>
                <x-form.label for="q" class="sr-only">Cari</x-form.label>
                <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari nama fakultas…" />
            </x-slot:search>
        </x-shared.filter-bar>

        @if ($faculties->isEmpty())
            <x-ui.empty-state icon="heroicon-o-building-library" title="Belum ada fakultas." />
        @else
            <x-ui.table>
                <x-slot:head>
                    <tr><th>Nama</th><th>Kode</th><th>Prodi</th><th>Status</th><th class="text-right"><span class="sr-only">Aksi</span></th></tr>
                </x-slot:head>
                @foreach ($faculties as $faculty)
                    <tr>
                        <td class="font-medium">{{ $faculty->name }}</td>
                        <td class="text-muted">{{ $faculty->code ?? '—' }}</td>
                        <td class="tabular-nums text-muted">{{ $faculty->study_programs_count }}</td>
                        <td><x-ui.badge :tone="$faculty->is_active ? 'success' : 'neutral'">{{ $faculty->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td class="text-right">
                            @include('master._row-actions', ['editUrl' => route('master.faculties.edit', $faculty), 'deleteUrl' => route('master.faculties.destroy', $faculty), 'name' => $faculty->name])
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $faculties->links() }}
    </x-ui.card>
</x-layouts.app>
