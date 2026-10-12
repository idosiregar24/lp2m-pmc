@php($isAdmin = auth()->user()->isAdmin())

<x-layouts.app title="Dashboard">
    {{-- PCR-Inspired 4 Hub Action Cards --}}
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Card 1: Pencarian --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-xl border border-line bg-surface shadow-xs transition hover:border-brand-300">
            <div class="flex items-center gap-2 bg-[#7a0d0d] px-4 py-2.5 text-white">
                <x-heroicon-o-magnifying-glass class="size-4 shrink-0" />
                <span class="text-xs font-bold uppercase tracking-wider">Pencarian</span>
            </div>
            <div class="flex flex-1 flex-col justify-between space-y-3 p-4">
                <p class="text-xs leading-relaxed text-muted">Data Penelitian, Pengabdian kepada Masyarakat dan Publikasi Ilmiah tiap Dosen</p>
                <form method="GET" action="{{ route('researches.index') }}" class="flex gap-1.5 pt-1">
                    <input type="text" name="q" placeholder="Tulis Inisial / Judul..." class="min-w-0 flex-1 rounded-lg border border-line bg-canvas px-2.5 py-1.5 text-xs text-ink placeholder:text-muted/70 focus:border-brand-200 focus:outline-none focus:ring-2 focus:ring-brand-600/10">
                    <button type="submit" class="rounded-lg bg-[#7a0d0d] px-3 py-1.5 text-xs font-semibold text-white hover:bg-brand-700 transition active:scale-[0.98]">Cari</button>
                </form>
            </div>
        </div>

        {{-- Card 2: Capaian --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-xl border border-line bg-surface shadow-xs transition hover:border-brand-300">
            <div class="flex items-center justify-between bg-[#7a0d0d] px-4 py-2.5 text-white">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-clipboard-document-check class="size-4 shrink-0" />
                    <span class="text-xs font-bold uppercase tracking-wider">Capaian</span>
                </div>
                @if ($isAdmin && $pendingCount > 0)
                    <span class="rounded-full bg-gold-400 px-2 py-0.2 text-[10px] font-bold text-gold-950 shadow-2xs">{{ $pendingCount }}</span>
                @endif
            </div>
            <div class="flex flex-1 flex-col justify-between space-y-3 p-4">
                <p class="text-xs leading-relaxed text-muted">Capaian Penelitian, Pengabdian kepada Masyarakat dan Publikasi Ilmiah Dosen setiap Tahun</p>
                <div class="pt-1">
                    <a href="{{ $isAdmin ? route('verifications.index') : route('researches.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-line bg-canvas px-3 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 active:scale-[0.98]">
                        <x-heroicon-o-calendar class="size-3.5" />
                        Capaian Tahun
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3: Statistik --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-xl border border-line bg-surface shadow-xs transition hover:border-brand-300">
            <div class="flex items-center gap-2 bg-[#7a0d0d] px-4 py-2.5 text-white">
                <x-heroicon-o-chart-bar class="size-4 shrink-0" />
                <span class="text-xs font-bold uppercase tracking-wider">Statistik</span>
            </div>
            <div class="flex flex-1 flex-col justify-between space-y-3 p-4">
                <p class="text-xs leading-relaxed text-muted">Statistik keseluruhan berdasarkan data Bagian Penelitian dan Pengabdian Masyarakat</p>
                <div class="pt-1">
                    <a href="{{ route('reports.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-line bg-canvas px-3 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 active:scale-[0.98]">
                        <x-heroicon-o-table-cells class="size-3.5" />
                        Statistik
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 4: Kinerja Dosen --}}
        <div class="flex flex-col justify-between overflow-hidden rounded-xl border border-line bg-surface shadow-xs transition hover:border-brand-300">
            <div class="flex items-center gap-2 bg-[#7a0d0d] px-4 py-2.5 text-white">
                <x-heroicon-o-user class="size-4 shrink-0" />
                <span class="text-xs font-bold uppercase tracking-wider">Kinerja Dosen</span>
            </div>
            <div class="flex flex-1 flex-col justify-between space-y-3 p-4">
                <p class="text-xs leading-relaxed text-muted">Halaman kinerja dosen menurut periode waktu tertentu. Klik untuk melihat detail informasi</p>
                <div class="pt-1">
                    <a href="{{ route('public.lecturers.index') }}" class="inline-flex w-full items-center justify-center gap-1.5 rounded-lg border border-line bg-canvas px-3 py-1.5 text-xs font-semibold text-ink shadow-2xs transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700 active:scale-[0.98]">
                        <x-heroicon-o-arrow-top-right-on-square class="size-3.5" />
                        Kinerja
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Overall Statistics Header & Analytics Section (PCR Overall Statistics Style) --}}
    <div class="grid gap-5 lg:grid-cols-12 items-start">
        {{-- Left Column: User Summary & KPI Status Panel --}}
        <div class="space-y-4 lg:col-span-4">
            {{-- Profile & Status Card (shadcn / Enterprise Style) --}}
            <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
                <div class="flex items-center justify-between border-b border-line bg-canvas/40 px-4 py-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Profil &amp; Status Kerja</span>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        Aktif
                    </span>
                </div>

                <div class="p-4 sm:p-5">
                    <div class="flex items-center gap-3.5">
                        <div class="flex size-11 shrink-0 items-center justify-center rounded-xl border border-slate-200/80 bg-slate-100 text-sm font-bold text-slate-800 shadow-2xs">
                            {{ auth()->user()->initials() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h2 class="truncate text-sm font-bold text-ink">{{ auth()->user()->name }}</h2>
                            <p class="mt-0.5 truncate text-xs text-muted">{{ $isAdmin ? 'LPPM Universitas PMC' : (auth()->user()->studyProgram?->displayName() ?? 'Dosen PMC') }}</p>
                        </div>
                    </div>

                    {{-- Unified Metric Strip --}}
                    <div class="mt-4 grid grid-cols-2 divide-x divide-line rounded-lg border border-line bg-canvas/60 p-2.5">
                        <div class="px-2">
                            <span class="block text-[11px] font-medium text-muted">Tahun Aktif</span>
                            <span class="mt-0.5 block text-base font-bold tabular-nums text-ink">{{ $year }}</span>
                        </div>
                        <div class="px-2 pl-3">
                            <span class="block text-[11px] font-medium text-muted">{{ $isAdmin ? 'Antrean' : 'Perlu Revisi' }}</span>
                            <span class="mt-0.5 block text-base font-bold tabular-nums {{ ($isAdmin ? $pendingCount : $needsRevision->count()) > 0 ? 'text-brand-700' : 'text-slate-700' }}">
                                {{ $isAdmin ? $pendingCount : $needsRevision->count() }} <span class="text-xs font-normal text-muted">berkas</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-line bg-canvas/30 px-4 py-3">
                    @if ($isAdmin)
                        <a href="{{ route('verifications.index') }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#7a0d0d] px-3.5 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-brand-700 active:scale-[0.99]">
                            <x-heroicon-o-check-badge class="size-4" />
                            <span>Tinjau Verifikasi ({{ $pendingCount }})</span>
                        </a>
                    @else
                        <a href="{{ route('researches.create') }}" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#7a0d0d] px-3.5 py-2 text-xs font-semibold text-white shadow-xs transition hover:bg-brand-700 active:scale-[0.99]">
                            <x-heroicon-o-plus class="size-4" />
                            <span>Tambah Data Baru</span>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Action List (Queue / Need Revision) --}}
            @if ($isAdmin)
                <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
                    <div class="flex items-center justify-between border-b border-line bg-canvas/40 px-4 py-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Menunggu Verifikasi</span>
                        <a href="{{ route('verifications.index') }}" class="text-xs font-medium text-brand-700 hover:underline">Semua</a>
                    </div>
                    @if ($pending->isEmpty())
                        <p class="p-4 text-center text-xs text-muted">Semua data telah diperiksa.</p>
                    @else
                        <ul class="divide-y divide-line">
                            @foreach ($pending->take(5) as $item)
                                <li class="p-3 transition hover:bg-slate-50/60">
                                    <a href="{{ $item['url'] }}" class="line-clamp-1 text-xs font-medium text-ink transition hover:text-brand-700">{{ $item['title'] }}</a>
                                    <p class="mt-0.5 text-[11px] text-muted">{{ $item['module'] }} · {{ $item['year'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @else
                <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
                    <div class="border-b border-line bg-canvas/40 px-4 py-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-700">Perlu Revisi</span>
                    </div>
                    @if ($needsRevision->isEmpty())
                        <p class="p-4 text-center text-xs text-muted">Tidak ada data yang perlu direvisi.</p>
                    @else
                        <ul class="divide-y divide-line">
                            @foreach ($needsRevision->take(5) as $item)
                                <li class="p-3 transition hover:bg-slate-50/60">
                                    <a href="{{ $item['url'] }}" class="line-clamp-1 text-xs font-medium text-ink transition hover:text-brand-700">{{ $item['title'] }}</a>
                                    <p class="mt-0.5 text-[11px] text-muted">{{ $item['module'] }} · {{ $item['year'] }}</p>
                                    @if ($item['note'])
                                        <p class="mt-1 line-clamp-1 text-[11px] text-warning-ink">{{ $item['note'] }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right Column: Main Charts (PCR Style Cards with Colored Header Bar) --}}
        <div class="space-y-5 lg:col-span-8">
            {{-- Main Chart: Total Penelitian, Pengabdian, Publikasi dan HKI --}}
            <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
                <div class="flex flex-wrap items-center justify-between gap-2 bg-[#7a0d0d] px-5 py-3 text-white">
                    <div>
                        <h2 class="text-sm font-bold tracking-tight">Total Penelitian, Pengabdian, Publikasi dan HKI</h2>
                        <p class="text-[11px] text-brand-100/90">Tren data terverifikasi {{ $chart['labels'][0] }}–{{ $year }} per modul</p>
                    </div>
                </div>
                <div class="p-5">
                    <div x-data="barChart(@js($chart))" class="h-64">
                        <canvas x-ref="canvas" role="img" aria-label="Grafik jumlah data terverifikasi per tahun"></canvas>
                    </div>
                    <details class="mt-3 text-xs">
                        <summary class="cursor-pointer font-medium text-muted hover:text-brand-700">Lihat rincian tabel angka</summary>
                        <x-ui.table class="mt-3">
                            <x-slot:head>
                                <tr>
                                    <th>Modul</th>
                                    @foreach ($chart['labels'] as $label)
                                        <th class="text-right">{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </x-slot:head>
                            @foreach ($chart['series'] as $series)
                                <tr>
                                    <td class="font-medium text-ink">{{ $series['label'] }}</td>
                                    @foreach ($series['data'] as $value)
                                        <td class="text-right tabular-nums">{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </x-ui.table>
                    </details>
                </div>
            </div>

            {{-- 2 Sub-charts in a Grid --}}
            <div class="grid gap-4 sm:grid-cols-2">
                {{-- Komposisi per Modul --}}
                <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
                    <div class="bg-[#7a0d0d] px-4 py-2.5 text-white">
                        <h3 class="text-xs font-bold uppercase tracking-wider">Komposisi per Modul</h3>
                    </div>
                    <div class="p-4">
                        @if (array_sum($composition['data']) > 0)
                            <div x-data="donutChart(@js($composition))" class="h-56">
                                <canvas x-ref="canvas" role="img" aria-label="Grafik komposisi data terverifikasi"></canvas>
                            </div>
                        @else
                            <p class="flex h-56 items-center justify-center text-xs text-muted">Belum ada data terverifikasi.</p>
                        @endif
                    </div>
                </div>

                {{-- Status Data per Modul --}}
                <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
                    <div class="bg-[#7a0d0d] px-4 py-2.5 text-white">
                        <h3 class="text-xs font-bold uppercase tracking-wider">Status Data per Modul</h3>
                    </div>
                    <div class="p-4">
                        <div x-data="barChart({ ...@js($statusChart), stacked: true, horizontal: true })" class="h-56">
                            <canvas x-ref="canvas" role="img" aria-label="Grafik status data per modul"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Numbers per module (5 KPI Tiles) --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-5">
        @foreach ($modules as $module)
            <x-ui.stat-card :label="$module['label'].' '.$year" :value="$module['verified_this_year']"
                :hint="'Total: '.$module['verified_total']" :icon="$module['icon']" :href="$module['url']" />
        @endforeach
    </div>

    {{-- Rekap per Fakultas Data Table --}}
    @if ($isAdmin)
        <div class="overflow-hidden rounded-xl border border-line bg-surface shadow-xs">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-line bg-canvas/40 px-5 py-3.5">
                <div>
                    <h2 class="text-sm font-bold text-ink">Rekap Capaian per Fakultas</h2>
                    <p class="text-xs text-muted">Data terverifikasi; satu data dihitung di setiap fakultas anggotanya</p>
                </div>
                <x-ui.button variant="secondary" size="sm" :href="route('reports.index')" icon="heroicon-o-table-cells">Rekap per Dosen</x-ui.button>
            </div>
            <x-ui.table>
                <x-slot:head>
                    <tr>
                        <th class="min-w-48">Fakultas</th>
                        @foreach ($modules as $module)
                            <th class="text-right">{{ $module['label'] }}</th>
                        @endforeach
                        <th class="text-right">Total</th>
                    </tr>
                </x-slot:head>
                @forelse ($facultyRecap as $row)
                    <tr class="transition hover:bg-slate-50/70">
                        <td class="font-medium text-ink">{{ $row['name'] }}</td>
                        @foreach ($row['counts'] as $count)
                            <td class="text-right tabular-nums {{ $count ? 'text-ink font-medium' : 'text-muted' }}">{{ $count }}</td>
                        @endforeach
                        <td class="text-right font-bold tabular-nums text-brand-700">{{ $row['total'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($modules) + 2 }}" class="text-center text-muted py-6">Belum ada fakultas.</td></tr>
                @endforelse
            </x-ui.table>
        </div>
    @else
        <div class="grid gap-4 lg:grid-cols-2">
            <x-ui.card title="Draf belum diajukan" description="Lengkapi dokumen lalu ajukan verifikasi.">
                @if ($drafts->isEmpty())
                    <p class="text-sm text-muted">Tidak ada draf.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($drafts as $item)
                            <li class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <a href="{{ $item['url'] }}" class="block truncate text-sm font-medium text-ink underline-offset-4 hover:underline">{{ $item['title'] }}</a>
                                    <p class="text-xs text-muted">{{ $item['module'] }} · {{ $item['year'] }}</p>
                                </div>
                                <x-ui.badge>Draf</x-ui.badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>

            <x-ui.card title="Data terbaru saya">
                @if ($latest->isEmpty())
                    <p class="text-sm text-muted">Belum ada data.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($latest as $item)
                            <li class="flex items-center justify-between gap-3 py-2.5 first:pt-0 last:pb-0">
                                <div class="min-w-0">
                                    <a href="{{ $item['url'] }}" class="block truncate text-sm font-medium text-ink underline-offset-4 hover:underline">{{ $item['title'] }}</a>
                                    <p class="text-xs text-muted">{{ $item['module'] }} · {{ $item['year'] }} · diperbarui {{ $item['updated_at']->translatedFormat('d F Y') }}</p>
                                </div>
                                <x-ui.badge :tone="$item['status']->tone()">{{ $item['status']->label() }}</x-ui.badge>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        </div>
    @endif
</x-layouts.app>
