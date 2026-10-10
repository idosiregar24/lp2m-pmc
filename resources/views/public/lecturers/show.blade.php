<x-layouts.public :title="$lecturer->name">
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted" aria-label="Breadcrumb">
        <a href="{{ route('public.lecturers.index') }}" class="transition hover:text-ink">Direktori Dosen</a>
        <x-heroicon-o-chevron-right class="size-3 text-muted/60" aria-hidden="true" />
        <span class="truncate font-semibold text-ink" aria-current="page">{{ $lecturer->name }}</span>
    </nav>

    {{-- Lecturer Profile Hero Card (Preskool ERP Profile Banner) --}}
    <section class="rounded-xl border border-slate-800 bg-slate-900 p-5 text-white shadow-xs sm:p-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            {{-- Profile info --}}
            <div class="flex items-start gap-4">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-xl border border-white/20 bg-brand-700 text-xl font-bold text-white shadow-xs">
                    {{ $lecturer->initials() }}
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded bg-brand-500/20 px-2 py-0.5 text-[11px] font-semibold text-brand-300 border border-brand-500/30">
                            #DOSEN-PMC
                        </span>
                        <span class="inline-flex items-center gap-1 rounded bg-emerald-500/20 px-2 py-0.5 text-[11px] font-semibold text-emerald-300 border border-emerald-500/30">
                            <span class="size-1.5 rounded-full bg-emerald-400"></span>
                            Dosen Aktif
                        </span>
                        <span class="text-xs text-slate-400">
                            Universitas Pekanbaru Medical Center
                        </span>
                    </div>

                    <h1 class="mt-1.5 text-xl font-bold tracking-tight text-white sm:text-2xl">
                        {{ $lecturer->name }}
                    </h1>

                    <p class="mt-0.5 text-xs text-slate-300">
                        {{ $lecturer->studyProgram?->displayName() ?? 'Program Studi' }}
                        @if ($lecturer->studyProgram?->faculty)
                            <span class="text-slate-600 mx-1">|</span>
                            <span class="text-slate-200">{{ $lecturer->studyProgram->faculty->name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Stat KPI Boxes (Preskool Metric Boxes) --}}
            <div class="grid grid-cols-3 gap-2 sm:grid-cols-6 lg:min-w-[32rem]">
                <div class="rounded-lg border border-white/10 bg-white/5 p-2.5 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Penelitian</span>
                    <span class="mt-1 block text-xl font-bold tabular-nums text-white">{{ $counts['research'] ?? 0 }}</span>
                    <span class="text-[10px] text-slate-400">Karya</span>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/5 p-2.5 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Pengabdian</span>
                    <span class="mt-1 block text-xl font-bold tabular-nums text-white">{{ $counts['community_service'] ?? 0 }}</span>
                    <span class="text-[10px] text-slate-400">Kegiatan</span>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/5 p-2.5 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Publikasi</span>
                    <span class="mt-1 block text-xl font-bold tabular-nums text-white">{{ $counts['publication'] ?? 0 }}</span>
                    <span class="text-[10px] text-slate-400">Artikel</span>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/5 p-2.5 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">Buku</span>
                    <span class="mt-1 block text-xl font-bold tabular-nums text-white">{{ $counts['book'] ?? 0 }}</span>
                    <span class="text-[10px] text-slate-400">Judul</span>
                </div>

                <div class="rounded-lg border border-white/10 bg-white/5 p-2.5 text-center">
                    <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider">HKI</span>
                    <span class="mt-1 block text-xl font-bold tabular-nums text-white">{{ $counts['intellectual_property'] ?? 0 }}</span>
                    <span class="text-[10px] text-slate-400">Sertifikat</span>
                </div>

                <div class="rounded-lg border border-brand-500/30 bg-brand-900/40 p-2.5 text-center">
                    <span class="text-[10px] font-semibold text-brand-300 uppercase tracking-wider">Total</span>
                    <span class="mt-1 block text-xl font-bold tabular-nums text-white">{{ $totalCounts }}</span>
                    <span class="text-[10px] text-brand-300">Luaran</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Lecturer Personal Analytics (Preskool Performance Charts) --}}
    @if ($totalCounts > 0)
        <section class="grid gap-4 lg:grid-cols-3" aria-label="Statistik Kinerja Dosen">
            <div class="rounded-xl border border-line bg-surface p-5 shadow-2xs lg:col-span-2">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-line pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-ink">Tren Kinerja {{ $lecturer->name }}</h2>
                        <p class="text-xs text-muted">Statistik data terverifikasi 5 tahun terakhir ({{ $chart['labels'][0] }}–{{ $year }})</p>
                    </div>
                    <span class="inline-flex items-center gap-1 rounded-md bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700 border border-brand-200">
                        <x-heroicon-o-chart-bar class="size-3.5" />
                        5 Tahun Terakhir
                    </span>
                </div>
                <div x-data="barChart(@js($chart))" class="mt-4 h-56">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik perbandingan capaian per tahun dosen"></canvas>
                </div>
            </div>

            <div class="rounded-xl border border-line bg-surface p-5 shadow-2xs flex flex-col justify-between">
                <div class="border-b border-line pb-3">
                    <h2 class="text-sm font-bold text-ink">Distribusi Tridharma Dosen</h2>
                    <p class="text-xs text-muted">Proporsi seluruh karya terverifikasi</p>
                </div>
                <div x-data="donutChart(@js($composition))" class="my-auto h-56 py-2">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik proporsi kategori karya dosen"></canvas>
                </div>
                <div class="border-t border-line pt-3 flex items-center justify-between text-xs text-muted">
                    <span>Total Capaian</span>
                    <span class="font-bold text-ink tabular-nums">{{ $totalCounts }} Karya</span>
                </div>
            </div>
        </section>
    @endif

    {{-- Module Tabs & Filter Toolbar --}}
    <section class="space-y-4">
        {{-- Module Tabs (Preskool Clean Underline Tabs) --}}
        <div class="border-b border-line bg-surface rounded-t-xl px-2">
            <nav class="-mb-px flex flex-wrap gap-2" aria-label="Modul Luaran">
                @foreach ($modules as $alias => $item)
                    @php($isActive = $alias === $type)
                    <a href="{{ route('public.lecturers.show', array_merge($filters, ['lecturer' => $lecturer, 'modul' => $alias])) }}"
                        @class([
                            'inline-flex items-center gap-2 border-b-2 px-3.5 py-3 text-xs font-semibold transition',
                            'border-brand-700 text-brand-700' => $isActive,
                            'border-transparent text-muted hover:border-line hover:text-ink' => ! $isActive,
                        ])>
                        <x-dynamic-component :component="$item['icon']" class="size-4" />
                        <span>{{ $item['label'] }}</span>
                        <span @class([
                            'rounded px-1.5 py-0.5 text-[10px] tabular-nums font-bold',
                            'bg-brand-50 text-brand-700 border border-brand-200' => $isActive,
                            'bg-canvas text-muted border border-line' => ! $isActive,
                        ])>
                            {{ $counts[$alias] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Search & Filters in this Lecturer's Works --}}
        <div class="rounded-xl border border-line bg-surface p-4 shadow-2xs">
            <form method="GET" action="{{ route('public.lecturers.show', $lecturer) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 items-end">
                <input type="hidden" name="modul" value="{{ $type }}">

                {{-- Search query input --}}
                <div class="lg:col-span-5">
                    <x-form.label for="q" class="text-xs font-semibold text-muted">Cari Judul / Topik</x-form.label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                            <x-heroicon-o-magnifying-glass class="size-4" />
                        </div>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                            placeholder="Cari judul {{ strtolower($module['label']) }}..."
                            class="block w-full rounded-lg border border-line bg-surface py-2 pl-9 pr-3 text-sm text-ink placeholder:text-muted/60 focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600" />
                    </div>
                </div>

                {{-- Year filter --}}
                <div class="lg:col-span-2">
                    <x-form.label for="year" class="text-xs font-semibold text-muted">Tahun</x-form.label>
                    <div class="mt-1">
                        <select name="year" id="year"
                            class="block w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                            <option value="">Semua Tahun</option>
                            @foreach ($years as $y)
                                <option value="{{ $y }}" @selected(($filters['year'] ?? '') == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Role filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="role" class="text-xs font-semibold text-muted">Peran Dosen</x-form.label>
                    <div class="mt-1">
                        <select name="role" id="role"
                            class="block w-full rounded-lg border border-line bg-surface px-3 py-2 text-sm text-ink focus:border-brand-600 focus:outline-none focus:ring-1 focus:ring-brand-600">
                            <option value="">Semua Peran</option>
                            <option value="lead" @selected(($filters['role'] ?? '') === 'lead')>Ketua / Penulis Pertama</option>
                            <option value="member" @selected(($filters['role'] ?? '') === 'member')>Anggota / Tim</option>
                        </select>
                    </div>
                </div>

                {{-- Submit & Reset Buttons --}}
                <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-brand-700 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-brand-800">
                        <x-heroicon-o-magnifying-glass class="size-4" />
                        <span>Filter</span>
                    </button>
                    @if (isset($filters['q']) || isset($filters['year']) || isset($filters['role']))
                        <a href="{{ route('public.lecturers.show', ['lecturer' => $lecturer, 'modul' => $type]) }}"
                            class="flex size-9.5 shrink-0 items-center justify-center rounded-lg border border-line bg-surface text-muted hover:bg-canvas hover:text-ink transition"
                            title="Reset Filter">
                            <x-heroicon-o-arrow-path class="size-4" />
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    {{-- Records List --}}
    @if ($records->isEmpty())
        <div class="rounded-xl border border-line bg-surface p-12 text-center shadow-2xs">
            <div class="mx-auto flex size-12 items-center justify-center rounded-xl bg-canvas text-muted">
                <x-dynamic-component :component="$module['icon']" class="size-6" />
            </div>
            <h3 class="mt-4 text-base font-semibold text-ink">Tidak ada data {{ strtolower($module['label']) }} ditemukan</h3>
            <p class="mt-1 text-sm text-muted">
                @if (isset($filters['q']) || isset($filters['year']) || isset($filters['role']))
                    Tidak ada luaran yang cocok dengan kriteria pencarian dan filter Anda.
                @else
                    Belum ada data {{ strtolower($module['label']) }} terverifikasi untuk dosen ini.
                @endif
            </p>
            @if (isset($filters['q']) || isset($filters['year']) || isset($filters['role']))
                <div class="mt-4">
                    <a href="{{ route('public.lecturers.show', ['lecturer' => $lecturer, 'modul' => $type]) }}"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-canvas px-4 py-2 text-xs font-semibold text-ink hover:bg-surface">
                        <x-heroicon-o-arrow-path class="size-3.5" />
                        Bersihkan Filter
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="space-y-3">
            <div class="flex items-center justify-between px-1">
                <p class="text-xs font-medium text-muted">
                    Menampilkan <span class="font-semibold text-ink">{{ $records->total() }}</span> {{ strtolower($module['label']) }} terverifikasi
                </p>
            </div>

            <ol class="space-y-3">
                @foreach ($records as $record)
                    @php($myContribution = $record->contributors->firstWhere('user_id', $lecturer->id))
                    @php($isLead = $myContribution && $myContribution->sort_order === 1)
                    <li class="rounded-xl border border-line bg-surface p-4.5 shadow-2xs transition hover:border-brand-300 hover:shadow-xs">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded bg-canvas border border-line px-2 py-0.5 text-xs font-semibold tabular-nums text-ink">
                                    <x-heroicon-o-calendar class="size-3.5 text-muted" />
                                    {{ $record->year }}
                                </span>

                                @if ($isLead)
                                    <span class="inline-flex items-center rounded border border-brand-200 bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700">
                                        Ketua / Penulis Pertama
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded border border-line bg-canvas px-2 py-0.5 text-xs font-medium text-muted">
                                        Anggota Tim
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1 rounded border border-success-line bg-success-soft px-2 py-0.5 text-xs font-semibold text-success-ink">
                                    <x-heroicon-o-check-badge class="size-3" />
                                    Terverifikasi LPPM
                                </span>
                            </div>
                        </div>

                        {{-- Title --}}
                        <h2 class="mt-2 text-sm font-bold text-ink leading-snug">
                            {{ $record->title }}
                        </h2>

                        {{-- Metadata Summary (Scheme, Funding, Publisher, etc.) --}}
                        @if ($summary = $record->publicSummary())
                            <p class="mt-1.5 text-xs leading-relaxed text-muted bg-canvas rounded-lg px-2.5 py-1.5 border border-line">
                                {{ $summary }}
                            </p>
                        @endif

                        {{-- Contributors List --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs border-t border-line pt-2.5">
                            <span class="font-semibold text-muted text-[11px]">Tim Peneliti/Penulis:</span>
                            @foreach ($record->contributors as $contributor)
                                <span @class([
                                    'inline-flex items-center gap-1 rounded px-2 py-0.5 text-xs',
                                    'bg-brand-50 text-brand-700 font-semibold border border-brand-200' => $contributor->user_id === $lecturer->id,
                                    'bg-canvas text-ink border border-line' => $contributor->user_id !== $lecturer->id,
                                ])>
                                    @if ($contributor->user?->isDosen() && $contributor->user->is_active)
                                        <a href="{{ route('public.lecturers.show', $contributor->user) }}" class="underline-offset-4 hover:underline">
                                            {{ $contributor->displayName() }}
                                        </a>
                                    @else
                                        {{ $contributor->displayName() }}
                                        @if ($contributor->isExternal() && $contributor->affiliation)
                                            <span class="text-muted text-[10px]">({{ $contributor->affiliation }})</span>
                                        @endif
                                    @endif
                                    @if ($contributor->sort_order === 1)
                                        <span class="text-[10px] text-muted font-normal">(Ketua)</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="pt-2">
                {{ $records->links() }}
            </div>
        </div>
    @endif
</x-layouts.public>
