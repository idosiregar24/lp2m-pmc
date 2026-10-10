@php($isAdmin = auth()->user()->isAdmin())

<x-layouts.app title="Dashboard">
    {{-- Preskool-inspired Profile Hero Banner & Quick Actions --}}
    <section class="grid gap-4 lg:grid-cols-12 items-stretch">
        <div class="relative overflow-hidden rounded-2xl border border-line bg-surface p-5 shadow-xs lg:col-span-8 flex flex-col justify-between">
            <div class="pointer-events-none absolute -right-10 -top-10 size-48 rounded-full bg-brand-50/60 blur-2xl" aria-hidden="true"></div>
            <div class="relative z-10 flex flex-wrap items-center gap-4">
                <span class="flex size-14 shrink-0 items-center justify-center rounded-2xl border border-brand-200 bg-brand-50 text-xl font-bold text-brand-700 shadow-xs">
                    {{ auth()->user()->initials() }}
                </span>
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center rounded-md border border-brand-200 bg-brand-50 px-2 py-0.5 text-[0.65rem] font-bold text-brand-700">
                            {{ auth()->user()->role->label() }}
                        </span>
                        <span class="text-xs text-muted">LPPM Universitas PMC</span>
                    </div>
                    <h1 class="mt-1 text-xl font-bold tracking-tight text-ink sm:text-2xl">
                        {{ auth()->user()->name }}
                    </h1>
                    <p class="text-xs text-muted">
                        {{ $isAdmin ? 'Administrator Sistem LPPM · Verifikasi & Manajemen Data' : (auth()->user()->studyProgram?->displayName() ?? 'Dosen Universitas Pekanbaru Medical Center') }}
                    </p>
                </div>
            </div>

            <div class="relative z-10 mt-4 flex flex-wrap items-center gap-2 border-t border-line/60 pt-3 text-xs">
                @if ($isAdmin)
                    <x-ui.button :href="route('verifications.index')" size="sm" icon="heroicon-o-check-circle">
                        Pemeriksaan Verifikasi ({{ $pendingCount }})
                    </x-ui.button>
                    <x-ui.button :href="route('reports.index')" variant="outline" size="sm" icon="heroicon-o-table-cells">
                        Rekapitulasi Kinerja
                    </x-ui.button>
                @else
                    <x-ui.button :href="route('researches.create')" size="sm" icon="heroicon-o-plus">
                        Tambah Penelitian
                    </x-ui.button>
                    <x-ui.button :href="route('community-services.create')" variant="outline" size="sm" icon="heroicon-o-plus">
                        Tambah Pengabdian
                    </x-ui.button>
                @endif
                <a href="{{ route('public.lecturers.index') }}"
                    class="ml-auto inline-flex items-center gap-1.5 rounded-xl border border-line bg-canvas px-3 py-1.5 font-semibold text-muted transition hover:bg-surface hover:text-ink">
                    <x-heroicon-o-arrow-top-right-on-square class="size-3.5" />
                    Portal Publik
                </a>
            </div>
        </div>

        {{-- Quick status widget / cards (like Preskool Leaves boxes) --}}
        <div class="grid grid-cols-2 gap-3 lg:col-span-4">
            @if ($isAdmin)
                <div class="flex flex-col justify-between rounded-2xl border border-warning-line bg-warning-soft/60 p-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-warning-ink">Antrean Verifikasi</span>
                        <div class="flex size-8 items-center justify-center rounded-xl bg-warning-line text-warning-ink">
                            <x-heroicon-o-clock class="size-4" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-2xl font-bold tabular-nums text-ink">{{ $pendingCount }}</span>
                        <p class="text-[0.65rem] text-muted">Perlu ditinjau LPPM</p>
                    </div>
                </div>
                <div class="flex flex-col justify-between rounded-2xl border border-info-line bg-info-soft/60 p-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-info-ink">Tahun Akademik</span>
                        <div class="flex size-8 items-center justify-center rounded-xl bg-info-line text-info-ink">
                            <x-heroicon-o-calendar class="size-4" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-2xl font-bold tabular-nums text-ink">{{ $year }}</span>
                        <p class="text-[0.65rem] text-muted">Tahun Berjalan</p>
                    </div>
                </div>
            @else
                <div class="flex flex-col justify-between rounded-2xl border border-danger-line bg-danger-soft/60 p-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-danger-ink">Perlu Revisi</span>
                        <div class="flex size-8 items-center justify-center rounded-xl bg-danger-line text-danger-ink">
                            <x-heroicon-o-exclamation-triangle class="size-4" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-2xl font-bold tabular-nums text-ink">{{ $needsRevision->count() }}</span>
                        <p class="text-[0.65rem] text-muted">Dikembalikan verifikator</p>
                    </div>
                </div>
                <div class="flex flex-col justify-between rounded-2xl border border-success-line bg-success-soft/60 p-4 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-semibold text-success-ink">Karya Terverifikasi</span>
                        <div class="flex size-8 items-center justify-center rounded-xl bg-success-line text-success-ink">
                            <x-heroicon-o-check-badge class="size-4" />
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-2xl font-bold tabular-nums text-ink">{{ array_sum($composition['data']) }}</span>
                        <p class="text-[0.65rem] text-muted">Total seluruh modul</p>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- Charts first, in a grid: trend (wide) + composition, workload (wide) + action list --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <x-ui.card :title="'Tren data terverifikasi'" :description="$chart['labels'][0].'–'.$year.', per modul'" class="lg:col-span-2">
            <div x-data="barChart(@js($chart))" class="h-64">
                <canvas x-ref="canvas" role="img" aria-label="Grafik jumlah data terverifikasi per tahun untuk setiap modul"></canvas>
            </div>
            <details class="mt-3 text-sm">
                <summary class="cursor-pointer text-muted hover:text-ink">Lihat angka</summary>
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
                            <td>{{ $series['label'] }}</td>
                            @foreach ($series['data'] as $value)
                                <td class="text-right tabular-nums">{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </x-ui.table>
            </details>
        </x-ui.card>

        <x-ui.card title="Komposisi per modul" description="Total data terverifikasi">
            @if (array_sum($composition['data']) > 0)
                <div x-data="donutChart(@js($composition))" class="h-64">
                    <canvas x-ref="canvas" role="img" aria-label="Grafik komposisi data terverifikasi per modul: {{ collect($composition['labels'])->zip($composition['data'])->map(fn ($pair) => $pair[0].' '.$pair[1])->implode(', ') }}"></canvas>
                </div>
            @else
                <p class="flex h-64 items-center justify-center text-sm text-muted">Belum ada data terverifikasi.</p>
            @endif
        </x-ui.card>

        <x-ui.card title="Status data per modul" description="Semua status, termasuk yang belum diverifikasi" class="lg:col-span-2">
            <div x-data="barChart({ ...@js($statusChart), stacked: true, horizontal: true })" class="h-72">
                <canvas x-ref="canvas" role="img" aria-label="Grafik jumlah data per status untuk setiap modul"></canvas>
            </div>
        </x-ui.card>

        @if ($isAdmin)
            <x-ui.card title="Menunggu verifikasi">
                <x-slot:actions>
                    <x-ui.button variant="ghost" size="sm" :href="route('verifications.index')">Lihat semua</x-ui.button>
                </x-slot:actions>
                <p class="text-3xl font-semibold tabular-nums text-ink">{{ $pendingCount }}</p>
                <p class="text-sm text-muted">data dosen perlu diperiksa</p>
                @if ($pending->isNotEmpty())
                    <ul class="mt-4 divide-y divide-line border-t border-line">
                        @foreach ($pending->take(4) as $item)
                            <li class="py-2.5">
                                <a href="{{ $item['url'] }}" class="line-clamp-1 text-sm font-medium text-ink underline-offset-4 hover:underline">{{ $item['title'] }}</a>
                                <p class="text-xs text-muted">{{ $item['module'] }} · {{ $item['year'] }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        @else
            <x-ui.card title="Perlu revisi" description="Dikembalikan admin — perbaiki lalu ajukan ulang.">
                @if ($needsRevision->isEmpty())
                    <p class="text-sm text-muted">Tidak ada data yang perlu direvisi.</p>
                @else
                    <ul class="divide-y divide-line">
                        @foreach ($needsRevision as $item)
                            <li class="py-2.5 first:pt-0 last:pb-0">
                                <a href="{{ $item['url'] }}" class="line-clamp-2 text-sm font-medium text-ink underline-offset-4 hover:underline">{{ $item['title'] }}</a>
                                <p class="text-xs text-muted">{{ $item['module'] }} · {{ $item['year'] }}</p>
                                @if ($item['note'])
                                    <p class="mt-1 line-clamp-2 text-xs text-warning-ink">{{ $item['note'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-ui.card>
        @endif
    </div>

    {{-- Numbers per module --}}
    <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-5">
        @foreach ($modules as $module)
            <x-ui.stat-card :label="$module['label'].' '.$year" :value="$module['verified_this_year']"
                :hint="'Total: '.$module['verified_total']" :icon="$module['icon']" :href="$module['url']" />
        @endforeach
    </div>

    @if ($isAdmin)
        <x-ui.card title="Rekap per fakultas" description="Data terverifikasi; satu data dihitung di setiap fakultas anggotanya." flush>
            <x-slot:actions>
                <x-ui.button variant="ghost" size="sm" :href="route('reports.index')">Rekap per dosen</x-ui.button>
            </x-slot:actions>
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
                    <tr>
                        <td class="font-medium text-ink">{{ $row['name'] }}</td>
                        @foreach ($row['counts'] as $count)
                            <td class="text-right tabular-nums {{ $count ? 'text-ink' : 'text-muted' }}">{{ $count }}</td>
                        @endforeach
                        <td class="text-right font-semibold tabular-nums text-ink">{{ $row['total'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="{{ count($modules) + 2 }}" class="text-center text-muted">Belum ada fakultas.</td></tr>
                @endforelse
            </x-ui.table>
        </x-ui.card>
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
