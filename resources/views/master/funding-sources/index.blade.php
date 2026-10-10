<x-layouts.app title="Sumber Dana" :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Sumber Dana']]">
    @include('master._header', ['createRoute' => 'master.funding-sources.create', 'createLabel' => 'Tambah Sumber Dana'])

    <x-ui.card flush>
        <x-shared.filter-bar :action="route('master.funding-sources.index')">
            <x-slot:search>
                <x-form.label for="q" class="sr-only">Cari</x-form.label>
                <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari sumber dana…" />
            </x-slot:search>
        </x-shared.filter-bar>

        @if ($fundingSources->isEmpty())
            <x-ui.empty-state icon="heroicon-o-banknotes" title="Belum ada sumber dana." />
        @else
            <x-ui.table>
                <x-slot:head>
                    <tr><th>Nama</th><th>Status</th><th class="text-right"><span class="sr-only">Aksi</span></th></tr>
                </x-slot:head>
                @foreach ($fundingSources as $fundingSource)
                    <tr>
                        <td class="font-medium">{{ $fundingSource->name }}</td>
                        <td><x-ui.badge :tone="$fundingSource->is_active ? 'success' : 'neutral'">{{ $fundingSource->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td class="text-right">
                            @include('master._row-actions', ['editUrl' => route('master.funding-sources.edit', $fundingSource), 'deleteUrl' => route('master.funding-sources.destroy', $fundingSource), 'name' => $fundingSource->name])
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $fundingSources->links() }}
    </x-ui.card>
</x-layouts.app>
