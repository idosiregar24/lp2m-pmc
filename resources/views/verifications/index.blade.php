<x-layouts.app title="Verifikasi">
    <x-shared.page-header title="Verifikasi Data" description="Data dosen yang menunggu diperiksa, paling lama diajukan di atas." icon="heroicon-o-clipboard-document-check" />

    @php
        $tabs = [[
            'label' => 'Semua',
            'url' => route('verifications.index', array_filter($filters)),
            'active' => $type === null,
            'count' => $counts->sum(),
        ]];
        foreach (\App\Support\Verifiable::MODULES as $alias => $module) {
            $tabs[] = [
                'label' => $module['label'],
                'url' => route('verifications.index', array_filter([...$filters, 'type' => $alias])),
                'active' => $type === $alias,
                'count' => $counts[$alias],
            ];
        }
    @endphp

    <x-ui.card flush>
        <div class="border-b border-line px-4 py-3 bg-canvas/40">
            <x-ui.tabs :items="$tabs" />
        </div>

        <x-shared.filter-bar :action="route('verifications.index')" :keep="['type']">
            @if ($type)
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
            <x-slot:search>
                <x-form.label for="q" class="sr-only">Cari</x-form.label>
                <x-form.input name="q" type="search" :value="$filters['q'] ?? null" placeholder="Cari judul atau nama anggota…" />
            </x-slot:search>
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

        @if ($records->isEmpty())
            <x-ui.empty-state icon="heroicon-o-check-badge" title="Tidak ada data yang menunggu verifikasi." description="Semua pengajuan sudah diperiksa." />
        @else
            <ul class="divide-y divide-line">
                @foreach ($records as $record)
                    @php($alias = $record->getMorphClass())
                    <li class="flex flex-col gap-3 p-4 transition-colors hover:bg-slate-50/60 sm:flex-row sm:items-center">
                        <div class="min-w-0 flex-1 space-y-1">
                            <div class="flex flex-wrap items-center gap-2 text-xs text-muted">
                                <x-ui.badge>{{ \App\Support\Verifiable::label($alias) }}</x-ui.badge>
                                <span class="font-medium text-slate-700 tabular-nums">{{ $record->year }}</span>
                                <span aria-hidden="true" class="text-slate-300">·</span>
                                <span>diajukan {{ $record->submitted_at?->translatedFormat('d M Y') }}</span>
                                <span aria-hidden="true" class="text-slate-300">·</span>
                                <span class="inline-flex items-center gap-1 rounded bg-brand-50 px-1.5 py-0.2 text-[11px] font-medium text-brand-700 border border-brand-200/60">{{ $record->documents_count }} dokumen</span>
                            </div>
                            <a href="{{ \App\Support\Verifiable::showUrl($record) }}" class="block font-medium text-ink transition-colors hover:text-brand-700">{{ $record->title }}</a>
                            <p class="text-xs text-muted">{{ $record->leadContributor?->displayName() }} · diinput oleh {{ $record->creator->name }}</p>
                        </div>
                        <x-ui.button variant="outline" size="sm" :href="\App\Support\Verifiable::showUrl($record)" icon="heroicon-o-eye">Periksa</x-ui.button>
                    </li>
                @endforeach
            </ul>
        @endif

        {{ $records->links() }}
    </x-ui.card>
</x-layouts.app>
