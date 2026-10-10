<x-layouts.public title="Direktori & Kinerja Dosen">
    {{-- Page Header & Academic Context --}}
    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-line pb-4">
        <div>
            <h1 class="text-xl font-bold tracking-tight text-ink sm:text-2xl">
                Direktori &amp; Kinerja Dosen
            </h1>
            <p class="text-xs text-muted">
                Portal Resmi LPPM Universitas Pekanbaru Medical Center · Data Kinerja Tridharma Terverifikasi
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-surface px-3 py-1.5 text-xs font-medium text-muted shadow-2xs">
                <x-heroicon-o-academic-cap class="size-4 text-brand-600" />
                <span>Tahun Akademik: {{ now()->year }} / {{ now()->year + 1 }}</span>
            </span>
        </div>
    </div>

    {{-- Summary Stats Grid (Preskool KPI Card Style) --}}
    <section class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6" aria-label="Statistik Kinerja LPPM">
        {{-- Dosen Aktif --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs transition hover:border-gray-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted">Dosen Aktif</span>
                <div class="flex size-9 items-center justify-center rounded-lg bg-navy-50 text-navy-700">
                    <x-heroicon-o-users class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-ink">{{ $totalLecturers ?? $lecturers->total() }}</p>
            <p class="mt-0.5 text-[11px] text-muted">Terdaftar LPPM</p>
        </div>

        {{-- Penelitian --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs transition hover:border-brand-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted">Penelitian</span>
                <div class="flex size-9 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                    <x-heroicon-o-beaker class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-brand-700">{{ $stats['research'] ?? 0 }}</p>
            <p class="mt-0.5 text-[11px] text-muted">Karya terverifikasi</p>
        </div>

        {{-- Pengabdian --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs transition hover:border-success-line">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted">Pengabdian</span>
                <div class="flex size-9 items-center justify-center rounded-lg bg-success-soft text-success-ink">
                    <x-heroicon-o-heart class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-success-ink">{{ $stats['community_service'] ?? 0 }}</p>
            <p class="mt-0.5 text-[11px] text-muted">Kegiatan terlaksana</p>
        </div>

        {{-- Publikasi --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs transition hover:border-info-line">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted">Publikasi</span>
                <div class="flex size-9 items-center justify-center rounded-lg bg-info-soft text-info-ink">
                    <x-heroicon-o-document-text class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-info-ink">{{ $stats['publication'] ?? 0 }}</p>
            <p class="mt-0.5 text-[11px] text-muted">Jurnal &amp; prosiding</p>
        </div>

        {{-- Buku --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs transition hover:border-warning-line">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted">Buku</span>
                <div class="flex size-9 items-center justify-center rounded-lg bg-warning-soft text-warning-ink">
                    <x-heroicon-o-book-open class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-warning-ink">{{ $stats['book'] ?? 0 }}</p>
            <p class="mt-0.5 text-[11px] text-muted">Buku ber-ISBN</p>
        </div>

        {{-- HKI --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs transition hover:border-purple-200">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted">HKI</span>
                <div class="flex size-9 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                    <x-heroicon-o-shield-check class="size-4.5" />
                </div>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-purple-700">{{ $stats['intellectual_property'] ?? 0 }}</p>
            <p class="mt-0.5 text-[11px] text-muted">Hak cipta &amp; paten</p>
        </div>
    </section>

    {{-- Statistics & Analytics Charts (Preskool ERP Statistics Section) --}}
    <section class="grid gap-4 lg:grid-cols-3" aria-label="Grafik Statistik LPPM">
        <div class="rounded-xl border border-line bg-surface p-5 shadow-2xs lg:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line pb-3">
                <div>
                    <h2 class="text-sm font-bold text-ink">Tren Kinerja Tridharma LPPM</h2>
                    <p class="text-xs text-muted">Statistik data terverifikasi 5 tahun terakhir ({{ $chart['labels'][0] }}–{{ $year }})</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-md bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700 border border-brand-200">
                    <x-heroicon-o-chart-bar class="size-3.5" />
                    5 Tahun Terakhir
                </span>
            </div>
            <div x-data="barChart(@js($chart))" class="mt-4 h-64">
                <canvas x-ref="canvas" role="img" aria-label="Grafik perbandingan capaian per tahun untuk seluruh modul luaran"></canvas>
            </div>
            <details class="mt-3 text-xs">
                <summary class="cursor-pointer font-medium text-muted hover:text-ink">Lihat rincian tabel per tahun</summary>
                <div class="mt-2 overflow-x-auto rounded-lg border border-line">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-canvas border-b border-line text-muted">
                            <tr>
                                <th class="px-3 py-2 font-semibold">Modul Luaran</th>
                                @foreach ($chart['labels'] as $label)
                                    <th class="px-3 py-2 text-right font-semibold">{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @foreach ($chart['series'] as $series)
                                <tr class="hover:bg-canvas/50">
                                    <td class="px-3 py-2 font-medium text-ink">{{ $series['label'] }}</td>
                                    @foreach ($series['data'] as $val)
                                        <td class="px-3 py-2 text-right tabular-nums text-muted">{{ $val }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </details>
        </div>

        <div class="rounded-xl border border-line bg-surface p-5 shadow-2xs flex flex-col justify-between">
            <div class="border-b border-line pb-3">
                <h2 class="text-sm font-bold text-ink">Komposisi Luaran Terverifikasi</h2>
                <p class="text-xs text-muted">Distribusi total {{ $totalOutputs }} karya ilmiah &amp; pengabdian</p>
            </div>
            @if ($totalOutputs > 0)
                <div x-data="donutChart(@js($composition))" class="my-auto h-64 py-2">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik persentase komposisi luaran LPPM"></canvas>
                </div>
            @else
                <div class="my-auto flex h-64 items-center justify-center text-xs text-muted">
                    Belum ada data terverifikasi.
                </div>
            @endif
            <div class="border-t border-line pt-3 flex items-center justify-between text-xs text-muted">
                <span>Total Luaran Resmi</span>
                <span class="font-bold text-ink tabular-nums">{{ $totalOutputs }} Dokumen</span>
            </div>
        </div>
    </section>

    {{-- Filter & Search Panel (Preskool Form Toolbar Style) --}}
    <section class="rounded-xl border border-line bg-surface shadow-2xs">
        <div class="flex items-center justify-between border-b border-line px-5 py-3.5">
            <div class="flex items-center gap-2">
                <x-heroicon-o-adjustments-horizontal class="size-4 text-muted" />
                <h2 class="text-xs font-bold uppercase tracking-wider text-muted">Pencarian &amp; Filter Dosen</h2>
            </div>
            @if (array_filter($filters))
                <a href="{{ route('public.lecturers.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-600 hover:text-brand-700">
                    <x-heroicon-o-arrow-path class="size-3.5" />
                    Reset Filter
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('public.lecturers.index') }}" class="p-5 space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 items-end">
                {{-- Search query input --}}
                <div class="lg:col-span-4">
                    <x-form.label for="q" class="text-xs font-semibold text-muted">Nama Dosen</x-form.label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                            <x-heroicon-o-magnifying-glass class="size-4" />
                        </div>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                            placeholder="Cari berdasarkan nama dosen..."
                            class="block w-full rounded-lg border border-line bg-surface py-2 pl-9 pr-3 text-sm text-ink placeholder:text-muted/60 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" />
                    </div>
                </div>

                {{-- Faculty filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="faculty" class="text-xs font-semibold text-muted">Fakultas</x-form.label>
                    <div class="mt-1">
                        <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua Fakultas"
                            class="rounded-lg border-line bg-surface py-2 text-sm text-ink focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" />
                    </div>
                </div>

                {{-- Study program filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="study_program" class="text-xs font-semibold text-muted">Program Studi</x-form.label>
                    <div class="mt-1">
                        <x-form.select name="study_program" :options="$studyPrograms" :value="$filters['study_program'] ?? null" placeholder="Semua Program Studi"
                            class="rounded-lg border-line bg-surface py-2 text-sm text-ink focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" />
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-brand-800">
                        <x-heroicon-o-magnifying-glass class="size-4" />
                        <span>Filter</span>
                    </button>
                    @if (array_filter($filters))
                        <a href="{{ route('public.lecturers.index') }}"
                            class="flex size-9.5 shrink-0 items-center justify-center rounded-lg border border-line bg-surface text-muted hover:bg-canvas hover:text-ink transition"
                            title="Reset Filter">
                            <x-heroicon-o-arrow-path class="size-4" />
                        </a>
                    @endif
                </div>
            </div>

            {{-- Secondary filter row: Luaran & Urutan --}}
            <div class="border-t border-line pt-3">
                <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="font-semibold text-muted mr-1">Kategori Luaran:</span>
                        <a href="{{ route('public.lecturers.index', array_merge($filters, ['has_modul' => null])) }}"
                            @class([
                                'rounded-lg px-2.5 py-1 font-medium transition',
                                'bg-brand-700 text-white shadow-xs' => empty($filters['has_modul']),
                                'bg-surface border border-line text-muted hover:bg-canvas hover:text-ink' => ! empty($filters['has_modul']),
                            ])>
                            Semua
                        </a>
                        @foreach ($modules as $modKey => $modInfo)
                            <a href="{{ route('public.lecturers.index', array_merge($filters, ['has_modul' => $modKey])) }}"
                                @class([
                                    'rounded-lg px-2.5 py-1 font-medium transition',
                                    'bg-brand-700 text-white shadow-xs' => ($filters['has_modul'] ?? '') === $modKey,
                                    'bg-surface border border-line text-muted hover:bg-canvas hover:text-ink' => ($filters['has_modul'] ?? '') !== $modKey,
                                ])>
                                {{ $modInfo['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2 ml-auto">
                        <span class="font-semibold text-muted">Urutan:</span>
                        <select name="sort" onchange="this.form.submit()"
                            class="rounded-lg border border-line bg-surface px-2.5 py-1 text-xs text-ink focus:border-brand-600 focus:outline-none">
                            <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Nama A – Z</option>
                            <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Nama Z – A</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </section>

    {{-- Results Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-1">
        <div class="flex items-center gap-2">
            <h2 class="text-sm font-bold text-ink">Daftar Dosen LPPM</h2>
            <span class="rounded-md bg-brand-50 border border-brand-200 px-2 py-0.5 text-xs font-semibold text-brand-700">
                {{ $lecturers->total() }} Dosen Ditemukan
            </span>
        </div>

        @if (array_filter($filters))
            <a href="{{ route('public.lecturers.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-brand-600 underline-offset-4 hover:underline">
                <x-heroicon-o-arrow-path class="size-3.5" />
                Reset semua filter
            </a>
        @endif
    </div>

    {{-- Lecturer Grid --}}
    @if ($lecturers->isEmpty())
        <div class="rounded-xl border border-line bg-surface p-12 text-center shadow-2xs">
            <div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-canvas text-muted">
                <x-heroicon-o-users class="size-6" />
            </div>
            <h3 class="mt-4 text-base font-semibold text-ink">Tidak ada data dosen yang sesuai</h3>
            <p class="mt-1 text-sm text-muted">Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.</p>
            <div class="mt-5">
                <a href="{{ route('public.lecturers.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-4 py-2 text-xs font-semibold text-ink hover:bg-surface">
                    <x-heroicon-o-arrow-path class="size-4" />
                    Reset Pencarian
                </a>
            </div>
        </div>
    @else
        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($lecturers as $lecturer)
                @php
                    $row = $rows[$lecturer->id];
                    $totalLecturerOutputs = array_sum($row['counts']);
                @endphp
                <li>
                    <div class="flex h-full flex-col justify-between rounded-xl border border-line bg-surface p-5 shadow-2xs transition hover:border-brand-300 hover:shadow-xs">
                        {{-- Lecturer Header --}}
                        <div class="flex items-start gap-3.5">
                            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-brand-200 bg-brand-50 text-sm font-bold text-brand-700">
                                {{ $lecturer->initials() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('public.lecturers.show', $lecturer) }}" class="block truncate font-bold text-ink hover:text-brand-700 transition">
                                    {{ $lecturer->name }}
                                </a>
                                <p class="mt-0.5 truncate text-xs text-muted">
                                    {{ $lecturer->studyProgram?->displayName() ?? 'Dosen Tetap' }}
                                </p>
                                @if ($lecturer->studyProgram?->faculty)
                                    <span class="mt-1 inline-block text-[11px] font-medium text-muted">
                                        {{ $lecturer->studyProgram->faculty->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Output Counter Matrix (Preskool Badge Styling) --}}
                        <div class="my-4 rounded-lg border border-line bg-canvas p-2.5">
                            <div class="mb-2 flex items-center justify-between text-[11px] font-semibold text-muted">
                                <span>Kinerja Terverifikasi</span>
                                <span class="rounded bg-brand-50 px-1.5 py-0.5 text-[10px] font-bold text-brand-700 border border-brand-200">Total: {{ $totalLecturerOutputs }}</span>
                            </div>
                            <div class="grid grid-cols-5 gap-1 text-center">
                                @foreach ($modules as $alias => $module)
                                    @php($c = $row['counts'][$alias])
                                    <div class="rounded bg-surface p-1.5 border border-line">
                                        <span class="block truncate text-[10px] font-medium text-muted" title="{{ $module['label'] }}">{{ $module['label'] }}</span>
                                        <span @class([
                                            'mt-0.5 block text-xs font-bold tabular-nums',
                                            'text-brand-700' => $c > 0 && $alias === 'research',
                                            'text-success-ink' => $c > 0 && $alias === 'community_service',
                                            'text-info-ink' => $c > 0 && $alias === 'publication',
                                            'text-warning-ink' => $c > 0 && $alias === 'book',
                                            'text-purple-700' => $c > 0 && $alias === 'intellectual_property',
                                            'text-muted/40' => $c === 0,
                                        ])>{{ $c }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action link --}}
                        <div class="mt-auto">
                            <a href="{{ route('public.lecturers.show', $lecturer) }}"
                                class="flex w-full items-center justify-between rounded-lg border border-line bg-surface px-3 py-2 text-xs font-semibold text-ink transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                                <span>Lihat Portofolio Lengkap</span>
                                <x-heroicon-o-chevron-right class="size-3.5" />
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        <div class="pt-4">
            {{ $lecturers->links() }}
        </div>
    @endif
</x-layouts.public>
