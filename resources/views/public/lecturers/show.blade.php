<x-layouts.public :title="$lecturer->name">
    {{-- Breadcrumbs --}}
    <nav class="flex items-center gap-1.5 text-xs text-muted" aria-label="Breadcrumb">
        <a href="{{ route('public.lecturers.index') }}" class="transition hover:text-ink">Direktori Dosen</a>
        <x-heroicon-o-chevron-right class="size-3 text-muted/60" aria-hidden="true" />
        <span class="truncate font-medium text-ink" aria-current="page">{{ $lecturer->name }}</span>
    </nav>

    {{-- Lecturer Profile Hero Card (Preskool Header Card Style) --}}
    <section class="relative overflow-hidden rounded-3xl border border-line bg-surface p-6 shadow-xs sm:p-8">
        <div class="pointer-events-none absolute -right-16 -top-16 size-64 rounded-full bg-brand-50/70 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-16 -left-16 size-64 rounded-full bg-navy-50/70 blur-3xl" aria-hidden="true"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            {{-- Profile info --}}
            <div class="flex items-start gap-4 sm:gap-5">
                <span class="flex size-16 shrink-0 items-center justify-center rounded-2xl border-2 border-brand-200 bg-brand-50 text-xl font-bold text-brand-700 shadow-sm sm:size-20 sm:text-2xl">
                    {{ $lecturer->initials() }}
                </span>
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1 rounded-full border border-success-line bg-success-soft px-2.5 py-0.5 text-[0.7rem] font-semibold text-success-ink">
                            <span class="size-1.5 rounded-full bg-success"></span>
                            Dosen Aktif
                        </span>
                        <span class="rounded-full border border-line bg-canvas px-2.5 py-0.5 text-[0.7rem] font-medium text-muted">
                            Universitas Pekanbaru Medical Center
                        </span>
                    </div>

                    <h1 class="mt-2 text-2xl font-bold tracking-tight text-ink sm:text-3xl">
                        {{ $lecturer->name }}
                    </h1>

                    <p class="mt-1 text-sm text-muted">
                        {{ $lecturer->studyProgram?->displayName() ?? 'Program Studi' }}
                        @if ($lecturer->studyProgram?->faculty)
                            <span class="text-line mx-1">|</span>
                            <span class="font-medium text-ink/80">{{ $lecturer->studyProgram->faculty->name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Stat Cards (Preskool Colored Widget Style) --}}
            <div class="grid grid-cols-2 gap-2.5 sm:grid-cols-3 lg:grid-cols-6 lg:min-w-[34rem]">
                <div class="rounded-2xl border border-brand-100 bg-brand-50/50 p-3 text-center transition hover:bg-brand-50">
                    <span class="text-[0.65rem] font-semibold text-brand-800 uppercase tracking-wider">Penelitian</span>
                    <span class="mt-1 block text-2xl font-bold tabular-nums text-brand-700">{{ $counts['research'] ?? 0 }}</span>
                    <span class="text-[0.65rem] text-muted">Karya</span>
                </div>

                <div class="rounded-2xl border border-success-line bg-success-soft/50 p-3 text-center transition hover:bg-success-soft">
                    <span class="text-[0.65rem] font-semibold text-success-ink uppercase tracking-wider">Pengabdian</span>
                    <span class="mt-1 block text-2xl font-bold tabular-nums text-success-ink">{{ $counts['community_service'] ?? 0 }}</span>
                    <span class="text-[0.65rem] text-muted">Kegiatan</span>
                </div>

                <div class="rounded-2xl border border-info-line bg-info-soft/50 p-3 text-center transition hover:bg-info-soft">
                    <span class="text-[0.65rem] font-semibold text-info-ink uppercase tracking-wider">Publikasi</span>
                    <span class="mt-1 block text-2xl font-bold tabular-nums text-info-ink">{{ $counts['publication'] ?? 0 }}</span>
                    <span class="text-[0.65rem] text-muted">Artikel</span>
                </div>

                <div class="rounded-2xl border border-warning-line bg-warning-soft/50 p-3 text-center transition hover:bg-warning-soft">
                    <span class="text-[0.65rem] font-semibold text-warning-ink uppercase tracking-wider">Buku</span>
                    <span class="mt-1 block text-2xl font-bold tabular-nums text-warning-ink">{{ $counts['book'] ?? 0 }}</span>
                    <span class="text-[0.65rem] text-muted">Judul</span>
                </div>

                <div class="rounded-2xl border border-purple-200 bg-purple-50/50 p-3 text-center transition hover:bg-purple-50">
                    <span class="text-[0.65rem] font-semibold text-purple-700 uppercase tracking-wider">HKI</span>
                    <span class="mt-1 block text-2xl font-bold tabular-nums text-purple-700">{{ $counts['intellectual_property'] ?? 0 }}</span>
                    <span class="text-[0.65rem] text-muted">Sertifikat</span>
                </div>

                <div class="rounded-2xl border border-line bg-canvas p-3 text-center">
                    <span class="text-[0.65rem] font-semibold text-muted uppercase tracking-wider">Total</span>
                    <span class="mt-1 block text-2xl font-bold tabular-nums text-ink">{{ $totalCounts }}</span>
                    <span class="text-[0.65rem] text-muted">Luaran</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Module Tabs & Filter Toolbar --}}
    <section class="space-y-4">
        {{-- Module Tabs --}}
        <div class="border-b border-line">
            <nav class="-mb-px flex flex-wrap gap-2 sm:gap-3" aria-label="Modul Luaran">
                @foreach ($modules as $alias => $item)
                    @php($isActive = $alias === $type)
                    <a href="{{ route('public.lecturers.show', array_merge($filters, ['lecturer' => $lecturer, 'modul' => $alias])) }}"
                        @class([
                            'inline-flex items-center gap-2 border-b-2 px-3.5 py-3 text-sm font-semibold transition',
                            'border-brand-600 text-brand-600' => $isActive,
                            'border-transparent text-muted hover:border-line hover:text-ink' => ! $isActive,
                        ])>
                        <x-dynamic-component :component="$item['icon']" class="size-4" />
                        <span>{{ $item['label'] }}</span>
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs tabular-nums',
                            'bg-brand-50 text-brand-700 font-bold' => $isActive,
                            'bg-canvas text-muted' => ! $isActive,
                        ])>
                            {{ $counts[$alias] ?? 0 }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </div>

        {{-- Search & Filters in this Lecturer's Works --}}
        <div class="rounded-2xl border border-line bg-surface p-4 shadow-xs">
            <form method="GET" action="{{ route('public.lecturers.show', $lecturer) }}" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12 items-end">
                <input type="hidden" name="modul" value="{{ $type }}">

                {{-- Search query input --}}
                <div class="lg:col-span-5">
                    <x-form.label for="q" class="text-xs font-medium text-muted">Cari Judul / Topik</x-form.label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                            <x-heroicon-o-magnifying-glass class="size-4" />
                        </div>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                            placeholder="Cari judul {{ strtolower($module['label']) }}..."
                            class="block w-full rounded-xl border border-line bg-canvas py-2 pl-9 pr-3 text-sm text-ink placeholder:text-muted/60 focus:border-brand-500 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-brand-500/20" />
                    </div>
                </div>

                {{-- Year filter --}}
                <div class="lg:col-span-2">
                    <x-form.label for="year" class="text-xs font-medium text-muted">Tahun</x-form.label>
                    <div class="mt-1">
                        <select name="year" id="year"
                            class="block w-full rounded-xl border border-line bg-canvas px-3 py-2 text-sm text-ink focus:border-brand-500 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <option value="">Semua Tahun</option>
                            @foreach ($years as $y)
                                <option value="{{ $y }}" @selected(($filters['year'] ?? '') == $y)>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Role filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="role" class="text-xs font-medium text-muted">Peran Dosen</x-form.label>
                    <div class="mt-1">
                        <select name="role" id="role"
                            class="block w-full rounded-xl border border-line bg-canvas px-3 py-2 text-sm text-ink focus:border-brand-500 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                            <option value="">Semua Peran</option>
                            <option value="lead" @selected(($filters['role'] ?? '') === 'lead')>Ketua / Penulis Pertama</option>
                            <option value="member" @selected(($filters['role'] ?? '') === 'member')>Anggota / Tim</option>
                        </select>
                    </div>
                </div>

                {{-- Submit & Reset Buttons --}}
                <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit"
                        class="flex flex-1 items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-brand-700 active:scale-[0.98]">
                        <x-heroicon-o-magnifying-glass class="size-4" />
                        <span>Filter</span>
                    </button>
                    @if (isset($filters['q']) || isset($filters['year']) || isset($filters['role']))
                        <a href="{{ route('public.lecturers.show', ['lecturer' => $lecturer, 'modul' => $type]) }}"
                            class="flex size-9.5 shrink-0 items-center justify-center rounded-xl border border-line bg-canvas text-muted hover:bg-surface hover:text-ink"
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
        <div class="rounded-3xl border border-line bg-surface p-12 text-center shadow-xs">
            <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-canvas text-muted">
                <x-dynamic-component :component="$module['icon']" class="size-8" />
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
                        class="inline-flex items-center gap-1.5 rounded-xl border border-line bg-canvas px-4 py-2 text-xs font-semibold text-ink hover:bg-surface">
                        <x-heroicon-o-arrow-path class="size-3.5" />
                        Bersihkan Filter
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="space-y-3.5">
            <div class="flex items-center justify-between px-1">
                <p class="text-xs font-medium text-muted">
                    Menampilkan <span class="font-semibold text-ink">{{ $records->total() }}</span> {{ strtolower($module['label']) }} terverifikasi
                </p>
            </div>

            <ol class="space-y-3">
                @foreach ($records as $record)
                    @php($myContribution = $record->contributors->firstWhere('user_id', $lecturer->id))
                    @php($isLead = $myContribution && $myContribution->sort_order === 1)
                    <li class="group rounded-2xl border border-line bg-surface p-5 shadow-xs transition duration-200 hover:border-brand-200 hover:shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center gap-1 rounded-md bg-canvas border border-line px-2 py-0.5 text-xs font-semibold tabular-nums text-ink">
                                    <x-heroicon-o-calendar class="size-3.5 text-muted" />
                                    {{ $record->year }}
                                </span>

                                @if ($isLead)
                                    <span class="inline-flex items-center rounded-md border border-brand-200 bg-brand-50 px-2 py-0.5 text-xs font-semibold text-brand-700">
                                        Ketua / Penulis Pertama
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-md border border-line bg-canvas px-2 py-0.5 text-xs font-medium text-muted">
                                        Anggota Tim
                                    </span>
                                @endif

                                <span class="inline-flex items-center gap-1 rounded-md border border-success-line bg-success-soft px-2 py-0.5 text-xs font-medium text-success-ink">
                                    <x-heroicon-o-check-badge class="size-3" />
                                    Terverifikasi LPPM
                                </span>
                            </div>
                        </div>

                        {{-- Title --}}
                        <h2 class="mt-2.5 text-base font-bold text-ink leading-snug">
                            {{ $record->title }}
                        </h2>

                        {{-- Metadata Summary (Scheme, Funding, Publisher, etc.) --}}
                        @if ($summary = $record->publicSummary())
                            <p class="mt-1.5 text-xs leading-relaxed text-muted bg-canvas/60 rounded-lg px-2.5 py-1.5 border border-line/60">
                                {{ $summary }}
                            </p>
                        @endif

                        {{-- Contributors List --}}
                        <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs border-t border-line/60 pt-3">
                            <span class="font-medium text-muted">Tim Peneliti/Penulis:</span>
                            @foreach ($record->contributors as $contributor)
                                <span @class([
                                    'inline-flex items-center gap-1 rounded-md px-2 py-0.5 text-xs',
                                    'bg-brand-50 text-brand-700 font-semibold border border-brand-200' => $contributor->user_id === $lecturer->id,
                                    'bg-canvas text-ink border border-line/60' => $contributor->user_id !== $lecturer->id,
                                ])>
                                    @if ($contributor->user?->isDosen() && $contributor->user->is_active)
                                        <a href="{{ route('public.lecturers.show', $contributor->user) }}" class="underline-offset-4 hover:underline">
                                            {{ $contributor->displayName() }}
                                        </a>
                                    @else
                                        {{ $contributor->displayName() }}
                                        @if ($contributor->isExternal() && $contributor->affiliation)
                                            <span class="text-muted text-[0.65rem]">({{ $contributor->affiliation }})</span>
                                        @endif
                                    @endif
                                    @if ($contributor->sort_order === 1)
                                        <span class="text-[0.6rem] text-muted font-normal">(Ketua)</span>
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
