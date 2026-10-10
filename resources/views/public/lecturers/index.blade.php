<x-layouts.public title="Direktori & Kinerja Dosen">
    {{-- Hero & Stats Section --}}
    <section class="relative overflow-hidden rounded-3xl border border-line bg-surface p-6 shadow-xs sm:p-8 lg:p-10">
        {{-- Decorative university backdrop circles --}}
        <div class="pointer-events-none absolute -right-20 -top-20 size-80 rounded-full bg-brand-50/60 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 size-80 rounded-full bg-navy-50/60 blur-3xl" aria-hidden="true"></div>

        <div class="relative z-10">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700">
                        <span class="size-2 rounded-full bg-brand-600 animate-pulse"></span>
                        LPPM Universitas Pekanbaru Medical Center
                    </div>
                    <h1 class="mt-3 text-2xl font-bold tracking-tight text-ink sm:text-3xl lg:text-4xl">
                        Portal Kinerja Tridharma Dosen
                    </h1>
                    <p class="mt-2 max-w-3xl text-sm leading-relaxed text-muted sm:text-base">
                        Data resmi terverifikasi luaran penelitian, pengabdian kepada masyarakat, publikasi ilmiah, buku referensi, dan Hak Kekayaan Intelektual (HKI) seluruh dosen Universitas PMC.
                    </p>
                </div>
            </div>

            {{-- Summary Stats Row (Preskool aesthetic) --}}
            <div class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                <div class="flex flex-col rounded-2xl border border-line bg-canvas p-4 transition hover:border-brand-200 hover:bg-surface">
                    <div class="flex items-center justify-between text-muted">
                        <span class="text-xs font-medium">Dosen Aktif</span>
                        <div class="flex size-7 items-center justify-center rounded-lg bg-navy-50 text-navy-700">
                            <x-heroicon-o-users class="size-4" />
                        </div>
                    </div>
                    <span class="mt-3 text-2xl font-bold tabular-nums text-ink">{{ $totalLecturers ?? $lecturers->total() }}</span>
                    <span class="mt-0.5 text-[0.7rem] text-muted">Terdaftar di sistem</span>
                </div>

                <div class="flex flex-col rounded-2xl border border-line bg-canvas p-4 transition hover:border-brand-200 hover:bg-surface">
                    <div class="flex items-center justify-between text-muted">
                        <span class="text-xs font-medium">Penelitian</span>
                        <div class="flex size-7 items-center justify-center rounded-lg bg-brand-50 text-brand-600">
                            <x-heroicon-o-beaker class="size-4" />
                        </div>
                    </div>
                    <span class="mt-3 text-2xl font-bold tabular-nums text-brand-700">{{ $stats['research'] ?? 0 }}</span>
                    <span class="mt-0.5 text-[0.7rem] text-muted">Terverifikasi</span>
                </div>

                <div class="flex flex-col rounded-2xl border border-line bg-canvas p-4 transition hover:border-brand-200 hover:bg-surface">
                    <div class="flex items-center justify-between text-muted">
                        <span class="text-xs font-medium">Pengabdian</span>
                        <div class="flex size-7 items-center justify-center rounded-lg bg-success-soft text-success-ink">
                            <x-heroicon-o-heart class="size-4" />
                        </div>
                    </div>
                    <span class="mt-3 text-2xl font-bold tabular-nums text-success-ink">{{ $stats['community_service'] ?? 0 }}</span>
                    <span class="mt-0.5 text-[0.7rem] text-muted">Terverifikasi</span>
                </div>

                <div class="flex flex-col rounded-2xl border border-line bg-canvas p-4 transition hover:border-brand-200 hover:bg-surface">
                    <div class="flex items-center justify-between text-muted">
                        <span class="text-xs font-medium">Publikasi</span>
                        <div class="flex size-7 items-center justify-center rounded-lg bg-info-soft text-info-ink">
                            <x-heroicon-o-document-text class="size-4" />
                        </div>
                    </div>
                    <span class="mt-3 text-2xl font-bold tabular-nums text-info-ink">{{ $stats['publication'] ?? 0 }}</span>
                    <span class="mt-0.5 text-[0.7rem] text-muted">Jurnal & prosiding</span>
                </div>

                <div class="flex flex-col rounded-2xl border border-line bg-canvas p-4 transition hover:border-brand-200 hover:bg-surface">
                    <div class="flex items-center justify-between text-muted">
                        <span class="text-xs font-medium">Buku</span>
                        <div class="flex size-7 items-center justify-center rounded-lg bg-gold-50 text-gold-800">
                            <x-heroicon-o-book-open class="size-4" />
                        </div>
                    </div>
                    <span class="mt-3 text-2xl font-bold tabular-nums text-gold-800">{{ $stats['book'] ?? 0 }}</span>
                    <span class="mt-0.5 text-[0.7rem] text-muted">Ber-ISBN</span>
                </div>

                <div class="flex flex-col rounded-2xl border border-line bg-canvas p-4 transition hover:border-brand-200 hover:bg-surface">
                    <div class="flex items-center justify-between text-muted">
                        <span class="text-xs font-medium">HKI</span>
                        <div class="flex size-7 items-center justify-center rounded-lg bg-purple-50 text-purple-700">
                            <x-heroicon-o-shield-check class="size-4" />
                        </div>
                    </div>
                    <span class="mt-3 text-2xl font-bold tabular-nums text-purple-700">{{ $stats['intellectual_property'] ?? 0 }}</span>
                    <span class="mt-0.5 text-[0.7rem] text-muted">Paten & cipta</span>
                </div>
            </div>
        </div>
    </section>

    {{-- Filter & Search Panel --}}
    <section class="rounded-2xl border border-line bg-surface p-5 shadow-xs sm:p-6" x-data="{ expanded: false }">
        <form method="GET" action="{{ route('public.lecturers.index') }}" class="space-y-4">
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-12">
                {{-- Search query input --}}
                <div class="lg:col-span-4">
                    <x-form.label for="q" class="text-xs font-medium text-muted">Cari Nama Dosen</x-form.label>
                    <div class="relative mt-1">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                            <x-heroicon-o-magnifying-glass class="size-4" />
                        </div>
                        <input type="search" name="q" id="q" value="{{ $filters['q'] ?? '' }}"
                            placeholder="Ketik nama dosen..."
                            class="block w-full rounded-xl border border-line bg-canvas py-2 pl-9 pr-3 text-sm text-ink placeholder:text-muted/60 focus:border-brand-500 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-brand-500/20" />
                    </div>
                </div>

                {{-- Faculty filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="faculty" class="text-xs font-medium text-muted">Fakultas</x-form.label>
                    <div class="mt-1">
                        <x-form.select name="faculty" :options="$faculties" :value="$filters['faculty'] ?? null" placeholder="Semua Fakultas"
                            class="rounded-xl border-line bg-canvas py-2 text-sm text-ink focus:border-brand-500 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-brand-500/20" />
                    </div>
                </div>

                {{-- Study program filter --}}
                <div class="lg:col-span-3">
                    <x-form.label for="study_program" class="text-xs font-medium text-muted">Program Studi</x-form.label>
                    <div class="mt-1">
                        <x-form.select name="study_program" :options="$studyPrograms" :value="$filters['study_program'] ?? null" placeholder="Semua Program Studi"
                            class="rounded-xl border-line bg-canvas py-2 text-sm text-ink focus:border-brand-500 focus:bg-surface focus:outline-none focus:ring-2 focus:ring-brand-500/20" />
                    </div>
                </div>

                {{-- Submit button --}}
                <div class="flex items-end gap-2 sm:col-span-2 lg:col-span-2">
                    <button type="submit"
                        class="flex w-full items-center justify-center gap-1.5 rounded-xl bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-xs transition hover:bg-brand-700 active:scale-[0.98]">
                        <x-heroicon-o-magnifying-glass class="size-4" />
                        <span>Filter</span>
                    </button>
                    <button type="button" @click="expanded = !expanded"
                        class="flex size-9.5 shrink-0 items-center justify-center rounded-xl border border-line bg-canvas text-muted hover:bg-surface hover:text-ink lg:hidden"
                        title="Filter Tambahan">
                        <x-heroicon-o-adjustments-horizontal class="size-4" />
                    </button>
                </div>
            </div>

            {{-- Secondary filter row: Luaran & Urutan --}}
            <div :class="expanded ? 'block' : 'hidden lg:block'" class="border-t border-line pt-3">
                <div class="flex flex-wrap items-center justify-between gap-3 text-xs">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="font-medium text-muted">Kategori Luaran:</span>
                        <a href="{{ route('public.lecturers.index', array_merge($filters, ['has_modul' => null])) }}"
                            @class([
                                'rounded-lg px-2.5 py-1 font-medium transition',
                                'bg-brand-600 text-white shadow-xs' => empty($filters['has_modul']),
                                'bg-canvas text-muted hover:bg-primary-100 hover:text-ink' => ! empty($filters['has_modul']),
                            ])>
                            Semua
                        </a>
                        @foreach ($modules as $modKey => $modInfo)
                            <a href="{{ route('public.lecturers.index', array_merge($filters, ['has_modul' => $modKey])) }}"
                                @class([
                                    'rounded-lg px-2.5 py-1 font-medium transition',
                                    'bg-brand-600 text-white shadow-xs' => ($filters['has_modul'] ?? '') === $modKey,
                                    'bg-canvas text-muted hover:bg-primary-100 hover:text-ink' => ($filters['has_modul'] ?? '') !== $modKey,
                                ])>
                                {{ $modInfo['label'] }}
                            </a>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-2 ml-auto">
                        <span class="font-medium text-muted">Urutan:</span>
                        <select name="sort" onchange="this.form.submit()"
                            class="rounded-lg border border-line bg-canvas px-2.5 py-1 text-xs text-ink focus:border-brand-500 focus:outline-none">
                            <option value="name_asc" @selected(($filters['sort'] ?? '') === 'name_asc')>Nama A – Z</option>
                            <option value="name_desc" @selected(($filters['sort'] ?? '') === 'name_desc')>Nama Z – A</option>
                        </select>
                    </div>
                </div>
            </div>
        </form>
    </section>

    {{-- Results Header & Reset Link --}}
    <div class="flex flex-wrap items-center justify-between gap-3 px-1">
        <div class="flex items-center gap-2">
            <h2 class="text-base font-bold text-ink">Daftar Dosen</h2>
            <span class="rounded-full bg-brand-50 border border-brand-200 px-2.5 py-0.5 text-xs font-semibold text-brand-700">
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
        <div class="rounded-3xl border border-line bg-surface p-12 text-center shadow-xs">
            <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                <x-heroicon-o-users class="size-8" />
            </div>
            <h3 class="mt-4 text-base font-semibold text-ink">Tidak ada data dosen yang sesuai</h3>
            <p class="mt-1 text-sm text-muted">Coba ubah kata kunci pencarian atau bersihkan filter yang aktif.</p>
            <div class="mt-5">
                <a href="{{ route('public.lecturers.index') }}"
                    class="inline-flex items-center gap-1.5 rounded-xl border border-line bg-canvas px-4 py-2 text-sm font-medium text-ink hover:bg-surface">
                    <x-heroicon-o-arrow-path class="size-4" />
                    Reset Pencarian
                </a>
            </div>
        </div>
    @else
        <ul class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($lecturers as $lecturer)
                @php
                    $row = $rows[$lecturer->id];
                    $totalLecturerOutputs = array_sum($row['counts']);
                @endphp
                <li class="group">
                    <div class="flex h-full flex-col rounded-2xl border border-line bg-surface p-5 shadow-xs transition duration-200 hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md">
                        {{-- Lecturer Header --}}
                        <div class="flex items-start gap-3.5">
                            <span class="flex size-12 shrink-0 items-center justify-center rounded-2xl border border-brand-200 bg-brand-50 text-sm font-bold text-brand-700 shadow-xs">
                                {{ $lecturer->initials() }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('public.lecturers.show', $lecturer) }}" class="block truncate font-semibold text-ink transition group-hover:text-brand-600">
                                    {{ $lecturer->name }}
                                </a>
                                <p class="mt-0.5 truncate text-xs text-muted">
                                    {{ $lecturer->studyProgram?->displayName() ?? 'Dosen Tetap' }}
                                </p>
                                @if ($lecturer->studyProgram?->faculty)
                                    <span class="mt-1.5 inline-block rounded-md border border-line bg-canvas px-1.5 py-0.5 text-[0.65rem] font-medium text-muted">
                                        {{ $lecturer->studyProgram->faculty->name }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Output Counter Matrix (Preskool Badge Styling) --}}
                        <div class="my-4 rounded-xl border border-line/70 bg-canvas/80 p-2.5">
                            <div class="mb-2 flex items-center justify-between text-[0.7rem] font-semibold text-muted">
                                <span>Kinerja Terverifikasi</span>
                                <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[0.65rem] font-bold text-brand-700">Total: {{ $totalLecturerOutputs }}</span>
                            </div>
                            <div class="grid grid-cols-5 gap-1.5 text-center">
                                @foreach ($modules as $alias => $module)
                                    @php($c = $row['counts'][$alias])
                                    <div class="rounded-lg bg-surface p-1.5 border border-line/60">
                                        <span class="block truncate text-[0.6rem] font-medium text-muted" title="{{ $module['label'] }}">{{ $module['label'] }}</span>
                                        <span @class([
                                            'mt-0.5 block text-sm font-bold tabular-nums',
                                            'text-brand-600' => $c > 0 && $alias === 'research',
                                            'text-success-ink' => $c > 0 && $alias === 'community_service',
                                            'text-info-ink' => $c > 0 && $alias === 'publication',
                                            'text-gold-800' => $c > 0 && $alias === 'book',
                                            'text-purple-700' => $c > 0 && $alias === 'intellectual_property',
                                            'text-muted/40' => $c === 0,
                                        ])>{{ $c }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action link --}}
                        <div class="mt-auto pt-2">
                            <a href="{{ route('public.lecturers.show', $lecturer) }}"
                                class="flex w-full items-center justify-between rounded-xl border border-line/80 bg-canvas px-3.5 py-2 text-xs font-semibold text-ink transition hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700">
                                <span>Lihat Portofolio Lengkap</span>
                                <x-heroicon-o-arrow-right class="size-3.5 transition group-hover:translate-x-0.5" />
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
