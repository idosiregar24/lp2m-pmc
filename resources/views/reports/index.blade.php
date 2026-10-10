<x-layouts.app title="Rekap">
    <x-shared.page-header title="Rekap per Dosen" description="Jumlah data terverifikasi yang mencantumkan setiap dosen sebagai anggota." icon="heroicon-o-chart-bar">
        <x-slot:actions>
            <x-ui.button variant="secondary" :href="route('reports.export', $filters)" icon="heroicon-o-arrow-down-tray" class="w-full sm:w-auto">Ekspor Excel</x-ui.button>
        </x-slot:actions>
    </x-shared.page-header>

    <x-ui.card flush>
        <x-shared.filter-bar :action="route('reports.index')">
            <div>
                <x-form.label for="lecturer" class="sr-only">Dosen</x-form.label>
                <x-form.select name="lecturer" :options="$lecturerOptions" :value="$filters['lecturer'] ?? null" placeholder="Semua dosen"
                    x-data="lecturerPicker" data-placeholder="Cari dosen…" />
            </div>
            <div>
                <x-form.label for="year" class="sr-only">Tahun</x-form.label>
                <x-form.select name="year" :options="$years" :value="$filters['year'] ?? null" placeholder="Semua tahun" />
            </div>
            <div>
                <x-form.label for="faculty" class="sr-only">Fakultas</x-form.label>
                <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua fakultas" />
            </div>
            <div>
                <x-form.label for="study_program" class="sr-only">Prodi</x-form.label>
                <x-form.select name="study_program" :options="$studyPrograms" :value="$filters['study_program'] ?? null" placeholder="Semua prodi" />
            </div>
        </x-shared.filter-bar>

        @if ($rows->isEmpty())
            <x-ui.empty-state icon="heroicon-o-chart-bar" title="Tidak ada dosen yang cocok dengan filter.">
                <x-ui.button variant="ghost" :href="route('reports.index')">Reset filter</x-ui.button>
            </x-ui.empty-state>
        @else
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="min-w-56">Dosen</th>
                        @foreach ($modules as $module)
                            <th class="text-right">{{ $module['label'] }}</th>
                        @endforeach
                        <th class="text-right">Total</th>
                    </tr>
                </x-slot:head>
                @foreach ($rows as $row)
                    <tr>
                        <td>
                            <a href="{{ route('users.show', $row['user']) }}" class="font-medium text-ink underline-offset-4 hover:underline">{{ $row['user']->name }}</a>
                            <span class="block text-xs text-muted">{{ $row['user']->studyProgram?->displayName() ?? '—' }}</span>
                        </td>
                        @foreach ($modules as $alias => $module)
                            <td class="text-right tabular-nums">
                                @if ($row['counts'][$alias])
                                    {{-- Drill down: the module list filtered to this lecturer's verified records. --}}
                                    <a href="{{ route($module['route'].'.index', array_filter(['lecturer' => $row['user']->id, 'status' => 'VERIFIED', 'year' => $filters['year'] ?? null])) }}"
                                        class="font-medium text-ink underline decoration-primary-200 underline-offset-4 hover:decoration-ink">{{ $row['counts'][$alias] }}</a>
                                @else
                                    <span class="text-muted">0</span>
                                @endif
                            </td>
                        @endforeach
                        <td class="text-right font-semibold tabular-nums text-ink">{{ $row['total'] }}</td>
                    </tr>
                @endforeach
            </x-ui.table>
        @endif

        {{ $lecturers->links() }}
    </x-ui.card>
</x-layouts.app>
