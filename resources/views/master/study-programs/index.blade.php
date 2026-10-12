<x-layouts.app title="Prodi" :breadcrumbs="[['label' => 'Data Master', 'url' => route('master.faculties.index')], ['label' => 'Prodi']]">
    @include('master._header', ['createRoute' => 'master.study-programs.create', 'createLabel' => 'Tambah Prodi'])

    <x-ui.card flush>
        <x-shared.filter-bar :action="route('master.study-programs.index')">
            <x-slot:search>
                <x-form.label for="q" class="sr-only">Cari</x-form.label>
                <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari nama prodi…" />
            </x-slot:search>
            <div>
                <x-form.label for="faculty" class="sr-only">Fakultas</x-form.label>
                <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua fakultas" />
            </div>
        </x-shared.filter-bar>

        @if ($studyPrograms->isEmpty())
            <x-ui.empty-state icon="heroicon-o-academic-cap" title="Belum ada prodi." />
        @else
            <x-ui.table>
                <x-slot:head>
                    <tr><th>Prodi</th><th>Jenjang</th><th>Fakultas</th><th>Pengguna</th><th>Status</th><th class="text-right"><span class="sr-only">Aksi</span></th></tr>
                </x-slot:head>
                @foreach ($studyPrograms as $studyProgram)
                    <tr>
                        <td class="font-medium text-ink">{{ $studyProgram->name }}</td>
                        <td><span class="inline-flex items-center rounded-md border border-line bg-canvas px-2 py-0.5 text-xs font-semibold text-slate-700">{{ $studyProgram->level->label() }}</span></td>
                        <td class="text-muted">{{ $studyProgram->faculty->name }}</td>
                        <td class="tabular-nums text-muted">{{ $studyProgram->users_count }}</td>
                        <td><x-ui.badge :tone="$studyProgram->is_active ? 'success' : 'neutral'">{{ $studyProgram->is_active ? 'Aktif' : 'Nonaktif' }}</x-ui.badge></td>
                        <td class="text-right">
                            @include('master._row-actions', ['editUrl' => route('master.study-programs.edit', $studyProgram), 'deleteUrl' => route('master.study-programs.destroy', $studyProgram), 'name' => $studyProgram->name])
                        </td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $studyPrograms->links() }}
    </x-ui.card>
</x-layouts.app>
