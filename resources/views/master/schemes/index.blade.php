<x-layouts.app title="Skema" :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Skema']]">
    @include('master._header', ['createRoute' => 'master.schemes.create', 'createLabel' => 'Tambah Skema'])

    <x-ui.card flush>
        <x-shared.filter-bar :action="route('master.schemes.index')">
            <x-slot:search>
                <x-form.label for="q" class="sr-only">Cari</x-form.label>
                <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari nama skema…" />
            </x-slot:search>
            <div>
                <x-form.label for="type" class="sr-only">Jenis</x-form.label>
                <x-form.select name="type" :options="$types" :value="$filters['type'] ?? null" placeholder="Semua jenis" />
            </div>
        </x-shared.filter-bar>

        @if ($schemes->isEmpty())
            <x-ui.empty-state icon="heroicon-o-rectangle-stack" title="Belum ada skema." />
        @else
            <x-ui.table>
                <x-slot:head>
                    <tr><th>Nama skema</th><th>Jenis</th><th>Status</th><th class="text-right"><span class="sr-only">Aksi</span></th></tr>
                </x-slot:head>
                @foreach ($schemes as $scheme)
                    <tr>
                        <td class="font-medium">{{ $scheme->name }}</td>
                        <td><x-ui.badge :tone="$scheme->type->tone()">{{ $scheme->type->label() }}</x-ui.badge></td>
                        <td><x-ui.badge :tone="$scheme->is_active ? 'success' : 'neutral'">{{ $scheme->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td class="text-right">
                            @include('master._row-actions', ['editUrl' => route('master.schemes.edit', $scheme), 'deleteUrl' => route('master.schemes.destroy', $scheme), 'name' => $scheme->name])
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $schemes->links() }}
    </x-ui.card>
</x-layouts.app>
