<x-layouts.app title="Impor Data Dosen">
    <x-shared.page-header title="Impor Data Dosen via Excel" description="Unggah berkas spreadsheet (.xlsx, .xls, .csv) untuk mendaftarkan atau memperbarui data dosen secara massal." icon="heroicon-o-arrow-up-tray">
        <x-slot:actions>
            <x-ui.button :href="route('users.index')" variant="outline" icon="heroicon-o-arrow-left">
                Kembali ke Daftar Pengguna
            </x-ui.button>
        </x-slot:actions>
    </x-shared.page-header>

    <div class="grid gap-6 lg:grid-cols-12 items-start">
        {{-- Form Upload Card --}}
        <div class="lg:col-span-7">
            <x-ui.card title="Unggah Berkas Spreadsheet">
                <form method="POST" action="{{ route('users.import.process') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <x-form.label for="file" required>Pilih Berkas Excel / CSV</x-form.label>
                        <div class="mt-2 flex justify-center rounded-xl border-2 border-dashed border-line px-6 py-8 transition hover:border-brand-500 bg-canvas/50">
                            <div class="text-center">
                                <x-heroicon-o-document-arrow-up class="mx-auto size-12 text-brand-600" />
                                <div class="mt-3 flex items-center justify-center text-sm leading-6 text-muted">
                                    <label for="file" class="relative cursor-pointer font-semibold text-brand-700 hover:text-brand-800 focus-within:outline-hidden">
                                        <span>Pilih berkas dari perangkat</span>
                                        <input id="file" name="file" type="file" accept=".xlsx,.xls,.csv" class="sr-only" required />
                                    </label>
                                </div>
                                <p class="text-xs text-muted mt-1">Mendukung format .xlsx, .xls, dan .csv (maksimal 10 MB)</p>
                            </div>
                        </div>
                        <x-form.error name="file" />
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
                        <x-ui.button type="submit" icon="heroicon-o-arrow-up-tray">
                            Mulai Impor Data
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
