<x-layouts.public title="Direktori & Kinerja Dosen">
    {{-- Institutional Hero Banner (PCR & PMC Identity Style) --}}
    <section class="overflow-hidden rounded-2xl border border-[#630909] bg-gradient-to-br from-[#7a0d0d] via-[#660909] to-[#4f0606] p-4 text-white shadow-xs sm:p-6 lg:p-8">
        <div class="max-w-3xl space-y-2 sm:space-y-3">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-md bg-white/15 px-2.5 py-0.5 text-[10px] font-bold text-white border border-white/20 uppercase tracking-wider">PORTAL TRIDHARMA</span>
                <span class="text-xs text-brand-200 font-medium">Universitas Pekanbaru Medical Center</span>
            </div>
            <h1 class="text-xl font-extrabold tracking-tight text-white sm:text-2xl lg:text-3xl">
                Direktori &amp; Kinerja Dosen LPPM
            </h1>
            <p class="text-xs leading-relaxed text-brand-100/90 sm:text-sm">
                Pusat data publik resmi penelitian, pengabdian kepada masyarakat, publikasi ilmiah, buku, dan perolehan HKI dosen yang telah diverifikasi oleh LPPM Universitas PMC.
            </p>
        </div>

        {{-- Direct Search Box in Hero Banner --}}
        <form method="GET" action="{{ route('public.lecturers.index') }}" class="mt-4 flex flex-col gap-2 sm:mt-6 sm:flex-row max-w-2xl">
            @if (!empty($filters['faculty']))
                <input type="hidden" name="faculty" value="{{ $filters['faculty'] }}">
            @endif
            @if (!empty($filters['study_program']))
                <input type="hidden" name="study_program" value="{{ $filters['study_program'] }}">
            @endif
            <div class="relative flex-1 min-w-0">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <x-heroicon-o-magnifying-glass class="size-4" />
                </div>
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari nama dosen / program studi..."
                    class="w-full rounded-xl border border-white/20 bg-white py-2.5 pl-10 pr-4 text-xs text-ink placeholder:text-slate-400 shadow-sm focus:border-gold-400 focus:outline-none focus:ring-2 focus:ring-gold-400/40 sm:text-sm">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-gold-400 px-5 py-2.5 text-xs font-bold text-gold-950 shadow-sm transition hover:bg-gold-500 active:scale-[0.98] sm:text-sm shrink-0">
                <x-heroicon-o-magnifying-glass class="size-4" />
                <span>Cari Dosen</span>
            </button>
        </form>
    </section>

    {{-- Summary Stats Grid (Cloudflare / shadcn KPI Cards) --}}
    <section class="grid grid-cols-2 gap-2.5 sm:gap-3 sm:grid-cols-3 lg:grid-cols-6 min-w-0 w-full" aria-label="Statistik Kinerja LPPM">
        {{-- Dosen Aktif --}}
        <div class="rounded-xl border border-line bg-surface p-3 sm:p-4 shadow-xs transition hover:border-brand-200 min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-medium text-muted truncate">Dosen Terdaftar</span>
                <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg border border-slate-200/80 bg-slate-100 text-slate-700 shadow-2xs">
                    <x-heroicon-o-users class="size-4 sm:size-4.5" />
                </div>
            </div>
            <p class="mt-2 sm:mt-3 text-xl sm:text-2xl font-bold tabular-nums text-ink">{{ $totalLecturers ?? $lecturers->total() }}</p>
            <p class="mt-0.5 text-[10px] sm:text-[11px] text-muted truncate">Tenaga pendidik aktif</p>
        </div>

        {{-- Penelitian --}}
        <div class="rounded-xl border border-line bg-surface p-3 sm:p-4 shadow-xs transition hover:border-brand-200 min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-medium text-muted truncate">Penelitian</span>
                <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg border border-brand-200/70 bg-brand-50 text-brand-700 shadow-2xs">
                    <x-heroicon-o-beaker class="size-4 sm:size-4.5" />
                </div>
            </div>
            <p class="mt-2 sm:mt-3 text-xl sm:text-2xl font-bold tabular-nums text-brand-700">{{ $stats['research'] ?? 0 }}</p>
            <p class="mt-0.5 text-[10px] sm:text-[11px] text-muted truncate">Karya terverifikasi</p>
        </div>

        {{-- Pengabdian --}}
        <div class="rounded-xl border border-line bg-surface p-3 sm:p-4 shadow-xs transition hover:border-success-line min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-medium text-muted truncate">Pengabdian</span>
                <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg border border-success-line bg-success-soft text-success-ink shadow-2xs">
                    <x-heroicon-o-heart class="size-4 sm:size-4.5" />
                </div>
            </div>
            <p class="mt-2 sm:mt-3 text-xl sm:text-2xl font-bold tabular-nums text-success-ink">{{ $stats['community_service'] ?? 0 }}</p>
            <p class="mt-0.5 text-[10px] sm:text-[11px] text-muted truncate">Kegiatan terlaksana</p>
        </div>

        {{-- Publikasi --}}
        <div class="rounded-xl border border-line bg-surface p-3 sm:p-4 shadow-xs transition hover:border-info-line min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-medium text-muted truncate">Publikasi</span>
                <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg border border-info-line bg-info-soft text-info-ink shadow-2xs">
                    <x-heroicon-o-document-text class="size-4 sm:size-4.5" />
                </div>
            </div>
            <p class="mt-2 sm:mt-3 text-xl sm:text-2xl font-bold tabular-nums text-info-ink">{{ $stats['publication'] ?? 0 }}</p>
            <p class="mt-0.5 text-[10px] sm:text-[11px] text-muted truncate">Jurnal &amp; prosiding</p>
        </div>

        {{-- Buku --}}
        <div class="rounded-xl border border-line bg-surface p-3 sm:p-4 shadow-xs transition hover:border-warning-line min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-medium text-muted truncate">Buku</span>
                <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg border border-warning-line bg-warning-soft text-warning-ink shadow-2xs">
                    <x-heroicon-o-book-open class="size-4 sm:size-4.5" />
                </div>
            </div>
            <p class="mt-2 sm:mt-3 text-xl sm:text-2xl font-bold tabular-nums text-warning-ink">{{ $stats['book'] ?? 0 }}</p>
            <p class="mt-0.5 text-[10px] sm:text-[11px] text-muted truncate">Buku ber-ISBN</p>
        </div>

        {{-- HKI --}}
        <div class="rounded-xl border border-line bg-surface p-3 sm:p-4 shadow-xs transition hover:border-purple-200 min-w-0">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-medium text-muted truncate">HKI</span>
                <div class="flex size-8 sm:size-9 shrink-0 items-center justify-center rounded-lg border border-purple-200 bg-purple-50 text-purple-700 shadow-2xs">
                    <x-heroicon-o-shield-check class="size-4 sm:size-4.5" />
                </div>
            </div>
            <p class="mt-2 sm:mt-3 text-xl sm:text-2xl font-bold tabular-nums text-purple-700">{{ $stats['intellectual_property'] ?? 0 }}</p>
            <p class="mt-0.5 text-[10px] sm:text-[11px] text-muted truncate">Hak cipta &amp; paten</p>
        </div>
    </section>

    {{-- Overall Statistics & Analytics Charts (PCR & Cloudflare Layout) --}}
    <section class="grid gap-5 lg:grid-cols-3 min-w-0 w-full" aria-label="Grafik Statistik LPPM">
        <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs lg:col-span-2 min-w-0 w-full">
            <div class="flex flex-wrap items-center justify-between gap-2 bg-[#7a0d0d] px-4 py-3 sm:px-5 text-white">
                <div class="min-w-0">
                    <h2 class="text-sm font-bold tracking-tight">Tren Kinerja Tridharma LPPM</h2>
                    <p class="text-[11px] text-brand-100/90 truncate">Statistik data terverifikasi {{ $chart['labels'][0] }}–{{ $year }} per modul luaran</p>
                </div>
                <span class="inline-flex items-center gap-1 rounded-md bg-white/15 px-2 py-0.5 text-xs font-semibold text-white border border-white/20 shrink-0">
                    <x-heroicon-o-chart-bar class="size-3.5" />
                    5 Tahun Terakhir
                </span>
            </div>
            <div class="p-4 sm:p-5 min-w-0">
                <div x-data="barChart(@js($chart))" class="relative h-64 w-full min-w-0">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik perbandingan capaian per tahun untuk seluruh modul luaran"></canvas>
                </div>
                <details class="mt-3 text-xs">
                    <summary class="cursor-pointer font-medium text-muted hover:text-brand-700">Lihat rincian tabel per tahun</summary>
                    <div class="mt-2 overflow-x-auto rounded-lg border border-line">
                        <table class="w-full text-left text-xs min-w-[320px]">
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
        </div>

        <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs flex flex-col justify-between min-w-0 w-full">
            <div class="bg-[#7a0d0d] px-4 py-3 sm:px-5 text-white">
                <h2 class="text-sm font-bold tracking-tight">Komposisi Luaran Terverifikasi</h2>
                <p class="text-[11px] text-brand-100/90 truncate">Distribusi total {{ $totalOutputs }} karya ilmiah &amp; pengabdian</p>
            </div>
            <div class="p-4 sm:p-5 flex-1 flex flex-col justify-center min-w-0">
                @if ($totalOutputs > 0)
                    <div x-data="donutChart(@js($composition))" class="relative h-56 w-full min-w-0 py-1">
                        <canvas x-ref="canvas" role="img" aria-label="Grafik persentase komposisi luaran LPPM"></canvas>
                    </div>
                @else
                    <div class="flex h-56 items-center justify-center text-xs text-muted">
                        Belum ada data terverifikasi.
                    </div>
                @endif
            </div>
            <div class="border-t border-line bg-canvas/40 px-4 py-3 sm:px-5 flex items-center justify-between text-xs text-muted">
                <span>Total Luaran Resmi</span>
                <span class="font-bold text-ink tabular-nums">{{ $totalOutputs }} Dokumen</span>
            </div>
        </div>
    </section>

    {{-- Filter & Search Toolbar (shadcn style segmented control) --}}
    <section class="rounded-xl border border-line bg-surface shadow-xs min-w-0 w-full overflow-hidden">
        <div class="flex items-center justify-between border-b border-line bg-canvas/40 px-4 py-3 sm:px-5 sm:py-3.5">
            <div class="flex items-center gap-2">
                <x-heroicon-o-adjustments-horizontal class="size-4 text-slate-500" />
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700">Pencarian &amp; Filter Fakultas</h2>
            </div>
            @if (array_filter($filters))
                <a href="{{ route('public.lecturers.index') }}" class="inline-flex items-center gap-1 text-xs font-semibold text-brand-700 hover:underline">
                    <x-heroicon-o-arrow-path class="size-3.5" />
                    Reset Filter
                </a>
            @endif
        </div>

        <form method="GET" action="{{ route('public.lecturers.index') }}" class="p-4 sm:p-5 space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 items-end">
                {{-- Search query input --}}
                <div class="lg:col-span-4">
                    <x-form.label for="q" class="text-xs font-semibold text-muted">Kata Kunci / Nama Dosen</x-form.label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                            <x-heroicon-o-magnifying-glass class="size-4" />
                        </div>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                            placeholder="Cari nama dosen..."
                            class="block w-full rounded-lg border border-line bg-surface py-2 pl-9 pr-3 text-xs text-ink placeholder:text-muted/60 focus:border-brand-200 focus:outline-none focus:ring-2 focus:ring-brand-600/10" />
                    </div>
                </div>

                {{-- Faculty filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="faculty" class="text-xs font-semibold text-muted">Fakultas</x-form.label>
                    <div class="mt-1">
                        <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua Fakultas"
                            class="rounded-lg border-line bg-surface py-2 text-xs text-ink focus:border-brand-200 focus:outline-none focus:ring-2 focus:ring-brand-600/10" />
                    </div>
                </div>

                {{-- Study program filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="study_program" class="text-xs font-semibold text-muted">Program Studi</x-form.label>
                    <div class="mt-1">
                        <x-form.select name="study_program" :options="$studyPrograms" :value="$filters['study_program'] ?? null" placeholder="Semua Program Studi"
                            class="rounded-lg border-line bg-surface py-2 text-xs text-ink focus:border-brand-200 focus:outline-none focus:ring-2 focus:ring-brand-600/10" />
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#7a0d0d] px-4 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-brand-700 active:scale-[0.98]">
                        <x-heroicon-o-funnel class="size-3.5" />
                        <span>Terapkan</span>
                    </button>
                    @if (array_filter($filters))
                        <a href="{{ route('public.lecturers.index') }}"
                            class="flex size-9 shrink-0 items-center justify-center rounded-lg border border-line bg-surface text-muted hover:bg-canvas hover:text-ink transition"
                            title="Reset Filter">
                            <x-heroicon-o-arrow-path class="size-4" />
                        </a>
                    @endif
                </div>
            </div>

            {{-- Secondary filter row: Segmented control for Module categories --}}
            <div class="border-t border-line pt-3.5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                    <div class="w-full sm:w-auto overflow-x-auto pb-1 sm:pb-0">
                        <div class="inline-flex items-center gap-1 rounded-lg border border-line bg-slate-100/80 p-1">
                            <span class="text-[11px] font-semibold text-muted px-2 shrink-0">Modul:</span>
                            <a href="{{ route('public.lecturers.index', array_merge($filters, ['has_modul' => null])) }}"
                                @class([
                                    'shrink-0 rounded-md px-2.5 py-1 text-xs font-medium transition-colors',
                                    'border border-brand-200/70 bg-surface font-semibold text-brand-700 shadow-xs' => empty($filters['has_modul']),
                                    'border border-transparent text-slate-600 hover:bg-surface/60 hover:text-brand-700' => ! empty($filters['has_modul']),
                                ])>
                                Semua
                            </a>
                            @foreach ($modules as $modKey => $modInfo)
                                <a href="{{ route('public.lecturers.index', array_merge($filters, ['has_modul' => $modKey])) }}"
                                    @class([
                                        'shrink-0 rounded-md px-2.5 py-1 text-xs font-medium transition-colors',
                                        'border border-brand-200/70 bg-surface font-semibold text-brand-700 shadow-xs' => ($filters['has_modul'] ?? '') === $modKey,
                                        'border border-transparent text-slate-600 hover:bg-surface/60 hover:text-brand-700' => ($filters['has_modul'] ?? '') !== $modKey,
                                    ])>
                                    {{ $modInfo['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-2 shrink-0">
                        <span class="text-xs font-medium text-muted">Urutan:</span>
                        <select name="sort" onchange="this.form.submit()"
                            class="rounded-lg border border-line bg-surface px-2.5 py-1 text-xs text-ink focus:border-brand-200 focus:outline-none">
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
            <h2 class="text-sm font-bold text-ink">Direktori Dosen LPPM</h2>
            <span class="rounded-md bg-brand-50 border border-brand-200/70 px-2 py-0.5 text-xs font-semibold text-brand-700">
                {{ $lecturers->total() }} Dosen Ditemukan
            </span>
        </div>

        @if (array_filter($filters))
            <a href="{{ route('public.lecturers.index') }}" class="inline-flex items-center gap-1 text-xs font-medium text-brand-700 underline-offset-4 hover:underline">
                <x-heroicon-o-arrow-path class="size-3.5" />
                Reset semua filter
            </a>
        @endif
    </div>

    {{-- Lecturer Grid --}}
    @if ($lecturers->isEmpty())
        <div class="rounded-xl border border-line bg-surface p-8 sm:p-12 text-center shadow-xs">
            <div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-canvas text-muted">
                <x-heroicon-o-users class="size-6" />
            </div>
            <h3 class="mt-4 text-base font-semibold text-ink">Tidak ada data dosen yang sesuai</h3>
            <p class="mt-1 text-xs text-muted">Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.</p>
            <div class="mt-5">
                <a href="{{ route('public.lecturers.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-4 py-2 text-xs font-semibold text-ink hover:bg-surface">
                    <x-heroicon-o-arrow-path class="size-4" />
                    Reset Pencarian
                </a>
            </div>
        </div>
    @else
        <ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 w-full min-w-0">
            @foreach ($lecturers as $lecturer)
                @php
                    $row = $rows[$lecturer->id];
                    $totalLecturerOutputs = array_sum($row['counts']);
                @endphp
                <li class="min-w-0 w-full">
                    <div class="flex h-full w-full min-w-0 flex-col justify-between rounded-xl border border-line bg-surface p-4 sm:p-5 shadow-xs transition hover:border-brand-200/90 hover:shadow-sm">
                        {{-- Lecturer Header --}}
                        <div class="flex items-start gap-3 sm:gap-3.5 min-w-0">
                            <span class="flex size-10 sm:size-11 shrink-0 items-center justify-center rounded-xl border border-brand-200/80 bg-brand-50 text-xs sm:text-sm font-bold text-brand-700 shadow-2xs">
                                {{ $lecturer->initials() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('public.lecturers.show', $lecturer) }}" class="block truncate font-bold text-sm sm:text-base text-ink hover:text-brand-700 transition" title="{{ $lecturer->name }}">
                                    {{ $lecturer->name }}
                                </a>
                                <p class="mt-0.5 truncate text-xs text-muted" title="{{ $lecturer->studyProgram?->displayName() ?? 'Dosen Tetap' }}">
                                    {{ $lecturer->studyProgram?->displayName() ?? 'Dosen Tetap' }}
                                </p>
                                @if ($lecturer->studyProgram?->faculty)
                                    <span class="mt-0.5 block truncate text-[11px] font-medium text-slate-500" title="{{ $lecturer->studyProgram->faculty->name }}">
                                        {{ $lecturer->studyProgram->faculty->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Output Counter Matrix --}}
                        <div class="my-3 sm:my-4 w-full min-w-0 rounded-lg border border-line bg-canvas/60 p-2 sm:p-2.5">
                            <div class="mb-2 flex items-center justify-between text-[11px] font-semibold text-muted">
                                <span>Kinerja Terverifikasi</span>
                                <span class="rounded bg-brand-50 px-1.5 py-0.5 text-[10px] font-bold text-brand-700 border border-brand-200/70 shrink-0">Total: {{ $totalLecturerOutputs }}</span>
                            </div>
                            <div class="grid grid-cols-5 gap-1 text-center w-full min-w-0">
                                @foreach ($modules as $alias => $module)
                                    @php($c = $row['counts'][$alias])
                                    <div class="min-w-0 rounded bg-surface p-1 sm:p-1.5 border border-line shadow-2xs">
                                        <span class="block truncate text-[9px] sm:text-[10px] font-medium text-muted" title="{{ $module['label'] }}">{{ $module['label'] }}</span>
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
                        <div class="mt-auto pt-1">
                            <a href="{{ route('public.lecturers.show', $lecturer) }}"
                                class="flex w-full items-center justify-between rounded-lg border border-line bg-surface px-3 py-2 text-xs font-semibold text-ink transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 shadow-2xs active:scale-[0.99]">
                                <span>Lihat Portofolio Lengkap</span>
                                <x-heroicon-o-chevron-right class="size-3.5 shrink-0" />
                            </a>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        <div class="pt-4 overflow-x-auto w-full">
            {{ $lecturers->links() }}
        </div>
    @endif
</x-layouts.public>
