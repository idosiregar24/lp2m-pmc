<x-layouts.app title="Impor Data Dosen">
    <x-shared.page-header title="Impor Data Dosen via Excel" description="Unggah berkas spreadsheet (.xlsx, .xls, .csv) untuk mendaftarkan atau memperbarui data dosen secara massal." icon="heroicon-o-arrow-up-tray">
        <x-slot:actions>
            <x-ui.button :href="route('users.index')" variant="outline" icon="heroicon-o-arrow-left">
                Kembali ke Daftar Pengguna
            </x-ui.button>
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-12 items-start"
        x-data="excelImporter({
            previewUrl: '{{ route('users.import.preview') }}',
            csrfToken: '{{ csrf_token() }}'
        })">

        {{-- Form Upload Card --}}
        <div class="lg:col-span-7 space-y-6">
            <x-ui.card title="Unggah Berkas Spreadsheet">
                <form method="POST" action="{{ route('users.import.process') }}" enctype="multipart/form-data" class="space-y-5" @submit="handleSubmit($event)">
                    @csrf

                    <div>
                        <x-form.label for="file" required>Pilih atau Tarik Berkas Excel / CSV</x-form.label>

                        {{-- Dropzone Area --}}
                        <div
                            class="relative mt-2 rounded-xl border-2 border-dashed p-6 transition-all duration-200 cursor-pointer select-none"
                            :class="{
                                'border-brand-500 bg-brand-500/10 ring-4 ring-brand-500/15 scale-[1.01]': isDragging,
                                'border-line bg-canvas/40 hover:border-brand-400 hover:bg-canvas/70': !isDragging && !file,
                                'border-brand-300 dark:border-brand-700/50 bg-surface': !isDragging && file
                            }"
                            @dragover.prevent="handleDragOver($event)"
                            @dragenter.prevent="handleDragEnter($event)"
                            @dragleave.prevent="handleDragLeave($event)"
                            @drop.prevent="handleDrop($event)"
                            @click="triggerFileInput"
                        >
                            {{-- Native Input (hidden) --}}
                            <input
                                x-ref="fileInput"
                                id="file"
                                name="file"
                                type="file"
                                accept=".xlsx,.xls,.csv"
                                class="sr-only"
                                @change="handleFileSelect($event)"
                                required
                            />

                            {{-- State A: No file chosen / Idle or Dragging --}}
                            <div x-show="!file" class="text-center py-4">
                                <div class="mx-auto flex size-14 items-center justify-center rounded-2xl bg-brand-50 dark:bg-brand-950/40 text-brand-700 transition duration-200"
                                     :class="{ 'scale-110 bg-brand-100 text-brand-800 animate-pulse': isDragging }">
                                    <x-heroicon-o-document-arrow-up class="size-8" />
                                </div>
                                <div class="mt-3">
                                    <p class="text-sm font-semibold text-ink" x-show="!isDragging">
                                        Tarik &amp; lepaskan berkas Excel di sini, atau <span class="text-brand-700 underline underline-offset-2">pilih dari perangkat</span>
                                    </p>
                                    <p class="text-sm font-bold text-brand-700" x-show="isDragging" x-cloak>
                                        Lepaskan berkas spreadsheet di sini untuk menganalisis dokumen...
                                    </p>
                                    <p class="text-xs text-muted mt-1.5">
                                        Mendukung spreadsheet <strong>.xlsx</strong>, <strong>.xls</strong>, dan <strong>.csv</strong> (maksimal 10 MB)
                                    </p>
                                </div>
                            </div>

                            {{-- State B: File Selected Preview Card --}}
                            <div x-show="file" x-cloak class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-2" @click.stop>
                                <div class="flex items-center gap-3.5 min-w-0">
                                    {{-- File Type Icon Badge --}}
                                    <div class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-xs ring-1 ring-emerald-500/20">
                                        <div class="flex flex-col items-center">
                                            <x-heroicon-o-table-cells class="size-5 mb-0.5" />
                                            <span x-text="fileInfo.type" class="text-[10px] tracking-wider uppercase font-mono leading-none"></span>
                                        </div>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-semibold text-ink truncate" x-text="fileInfo.name" :title="fileInfo.name"></p>
                                        <div class="flex items-center gap-2 mt-0.5 text-xs text-muted">
                                            <span x-text="fileInfo.size" class="font-medium"></span>
                                            <span>&bull;</span>
                                            <span class="inline-flex items-center gap-1 text-emerald-700 dark:text-emerald-400 font-medium">
                                                <x-heroicon-s-check-circle class="size-3.5 text-emerald-600 dark:text-emerald-400" />
                                                Berkas terpilih
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                                    <button
                                        type="button"
                                        @click="triggerFileInput"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-line bg-surface px-2.5 py-1.5 text-xs font-medium text-ink hover:bg-canvas transition cursor-pointer"
                                        title="Pilih berkas lain"
                                    >
                                        <x-heroicon-o-arrow-path class="size-3.5 text-muted" />
                                        Ganti Berkas
                                    </button>
                                    <button
                                        type="button"
                                        @click="removeFile"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-danger/30 bg-danger/5 px-2.5 py-1.5 text-xs font-medium text-danger-ink hover:bg-danger/10 transition cursor-pointer"
                                        title="Hapus berkas yang dipilih"
                                    >
                                        <x-heroicon-o-trash class="size-3.5" />
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Client / Server Error Box --}}
                        <div x-show="errorMessage" x-cloak class="mt-2.5 rounded-lg border border-danger/30 bg-danger/10 p-3 text-xs text-danger-ink flex items-start gap-2.5">
                            <x-heroicon-o-exclamation-triangle class="size-4 shrink-0 mt-0.5" />
                            <span x-text="errorMessage"></span>
                        </div>
                        <x-form.error name="file" />
                    </div>

                    {{-- Loading analysis state --}}
                    <div x-show="loading" x-cloak class="rounded-xl border border-line bg-canvas/60 p-5 text-center space-y-2">
                        <div class="inline-flex items-center justify-center p-2 rounded-full bg-brand-500/10 text-brand-700">
                            <svg class="animate-spin size-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <p class="text-xs font-semibold text-ink">Membaca &amp; menganalisis lembar spreadsheet...</p>
                        <p class="text-[11px] text-muted">Memverifikasi kesesuaian kolom dan baris data dosen.</p>
                    </div>

                    {{-- Pratinjau Dokumen Isi Spreadsheet --}}
                    <div x-show="previewData && !loading" x-cloak class="space-y-3 rounded-xl border border-line bg-canvas/30 p-4">
                        <div class="flex items-center justify-between border-b border-line pb-2.5">
                            <div class="flex items-center gap-2">
                                <span class="flex size-2 rounded-full bg-emerald-500"></span>
                                <h4 class="text-xs font-bold text-ink uppercase tracking-wider">
                                    Pratinjau Isi Dokumen (<span x-text="previewData?.total_rows"></span> Baris Terdeteksi)
                                </h4>
                            </div>
                            <span class="text-[11px] text-muted">
                                Judul pada Baris #<span x-text="previewData?.header_row"></span>
                            </span>
                        </div>

                        {{-- Kolom Mapping Status Badges --}}
                        <div class="flex flex-wrap gap-1.5 text-[11px]">
                            <template x-for="(col, key) in previewData?.columns" :key="key">
                                <span class="inline-flex items-center gap-1 rounded-md bg-surface px-2 py-0.5 font-medium border border-line text-ink">
                                    <span class="font-bold text-brand-700" x-text="col.letter"></span>:
                                    <span x-text="col.name"></span>
                                </span>
                            </template>
                        </div>

                        {{-- Table Preview Snippet --}}
                        <div class="overflow-x-auto rounded-lg border border-line bg-surface">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-canvas border-b border-line text-muted">
                                    <tr>
                                        <th class="px-2.5 py-2 font-semibold w-10 text-center">#</th>
                                        <th class="px-2.5 py-2 font-semibold">Fakultas</th>
                                        <th class="px-2.5 py-2 font-semibold">Program Studi</th>
                                        <th class="px-2.5 py-2 font-semibold">Nama Dosen</th>
                                        <th class="px-2.5 py-2 font-semibold">NIDN</th>
                                        <th class="px-2.5 py-2 font-semibold">NUPTK</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-line text-ink">
                                    <template x-for="(row, idx) in previewData?.sample_rows" :key="idx">
                                        <tr :class="!row.valid ? 'bg-danger/5' : 'hover:bg-canvas/40'">
                                            <td class="px-2.5 py-1.5 font-mono text-center text-muted text-[11px]" x-text="row.row_num"></td>
                                            <td class="px-2.5 py-1.5 truncate max-w-[120px]" x-text="row.fakultas || '-'" :title="row.fakultas"></td>
                                            <td class="px-2.5 py-1.5 truncate max-w-[120px]" x-text="row.prodi || '-'" :title="row.prodi"></td>
                                            <td class="px-2.5 py-1.5 font-medium" :class="row.valid ? 'text-ink' : 'text-danger-ink'">
                                                <span x-text="row.nama || '(Nama kosong)'"></span>
                                            </td>
                                            <td class="px-2.5 py-1.5 font-mono text-[11px] text-muted" x-text="row.nidn || '-'"></td>
                                            <td class="px-2.5 py-1.5 font-mono text-[11px] text-muted" x-text="row.nuptk || '-'"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <p class="text-[11px] text-muted italic">
                            * Menampilkan pratinjau hingga <span x-text="previewData?.sample_rows?.length"></span> baris pertama. Seluruh <span x-text="previewData?.total_rows"></span> baris data akan diimpor saat Anda menekan tombol di bawah.
                        </p>
                    </div>

                    <div class="rounded-lg border border-line bg-canvas p-4 space-y-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" name="update_existing" value="1" checked
                                class="mt-0.5 size-4 rounded-sm border-line text-brand-600 focus:ring-brand-500" />
                            <span class="text-xs text-ink leading-relaxed">
                                <strong class="font-semibold block">Perbarui data dosen jika sudah terdaftar</strong>
                                Jika NIDN/NUPTK atau nama dosen cocok dengan data yang sudah ada, unit fakultas dan prodi akan diperbarui tanpa membuat akun duplikat.
                            </span>
                        </label>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-line">
                        <x-ui.button type="submit" icon="heroicon-o-arrow-up-tray" ::disabled="!file || isSubmitting">
                            <span x-show="!isSubmitting">Mulai Impor Data</span>
                            <span x-show="isSubmitting" x-cloak>Memproses Impor...</span>
                        </x-ui.button>

                        <x-ui.button :href="route('users.import.template')" variant="outline" icon="heroicon-o-arrow-down-tray">
                            Unduh Template Excel
                        </x-ui.button>
                    </div>
                </form>
            </x-ui.card>
        </div>

        {{-- Panduan Format Kolom Card --}}
        <div class="lg:col-span-5 space-y-4">
            <x-ui.card title="Format Kolom Spreadsheet">
                <p class="text-xs text-muted mb-4 leading-relaxed">
                    Pastikan susunan kolom pada spreadsheet Anda mengikuti urutan berikut (baris pertama sebagai judul kolom):
                </p>

                <div class="overflow-x-auto rounded-lg border border-line">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-canvas border-b border-line text-muted">
                            <tr>
                                <th class="px-3 py-2 font-semibold">Kolom</th>
                                <th class="px-3 py-2 font-semibold">Nama Kolom</th>
                                <th class="px-3 py-2 font-semibold">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line text-ink">
                            <tr>
                                <td class="px-3 py-2 font-bold text-brand-700">A</td>
                                <td class="px-3 py-2 font-semibold">Fakultas</td>
                                <td class="px-3 py-2 text-muted">Contoh: <em>Kesehatan</em>, <em>Ekonomi dan Bisnis</em></td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-bold text-brand-700">B</td>
                                <td class="px-3 py-2 font-semibold">Prodi</td>
                                <td class="px-3 py-2 text-muted">Contoh: <em>Gizi</em>, <em>Ilmu Keperawatan</em>, <em>Bisnis Digital</em></td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-bold text-brand-700">C</td>
                                <td class="px-3 py-2 font-semibold">Nama Dosen</td>
                                <td class="px-3 py-2 text-muted">Nama lengkap beserta gelar akademik</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-bold text-brand-700">D</td>
                                <td class="px-3 py-2 font-semibold">NIDN</td>
                                <td class="px-3 py-2 text-muted">10 digit angka (boleh kosong / strip jika belum ada)</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 font-bold text-brand-700">E</td>
                                <td class="px-3 py-2 font-semibold">NUPTK</td>
                                <td class="px-3 py-2 text-muted">16 digit angka (boleh kosong / strip jika belum ada)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 space-y-2.5 text-xs text-muted leading-relaxed">
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-check-circle class="size-4 shrink-0 text-success-ink mt-0.5" />
                        <span><strong>Otomasi Fakultas &amp; Prodi:</strong> Jika nama fakultas atau program studi belum terdaftar di sistem, sistem akan otomatis mendaftarkannya.</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <x-heroicon-o-key class="size-4 shrink-0 text-brand-600 mt-0.5" />
                        <span><strong>Akun &amp; Password Awal:</strong> Dosen baru otomatis dibuatkan username resmi dan password awal <code>pmc12345</code> (wajib diganti saat login pertama).</span>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
