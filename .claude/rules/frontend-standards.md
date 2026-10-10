# Frontend Standards — Blade + Tailwind v4 + Alpine.js

Berlaku untuk `resources/views/`, `resources/css/`, `resources/js/`. Visual
& warna: [`design-standards.md`](design-standards.md). Komponen baru: skill
`blade-component`. Halaman baru: skill `front-end-design`.

## 1. Struktur folder

```
resources/
├── css/app.css                     # @import tailwindcss + @theme token PMC (satu-satunya sumber warna)
├── js/
│   ├── app.js                      # Alpine + plugin focus, Alpine.data() registrasi, Tom Select init
│   └── components/                 # logika Alpine >10 baris: contributorsField.js, documentsField.js
└── views/
    ├── components/
    │   ├── layouts/                # app (sidebar+topbar), guest (login, ganti password paksa)
    │   ├── ui/                     # button, badge, card, table, modal, alert, empty-state, stat-card, tabs, logo
    │   ├── form/                   # label, input, select, textarea, file, error, field
    │   └── shared/                 # page-header, filter-bar, contributors-field, contributors-list,
    │                               # documents-field, documents-list, detail-list, delete-button,
    │                               # verification-panel (status + aksi + riwayat)
    ├── auth/                       # login, force-password
    ├── profile/                    # show
    ├── dashboard.blade.php
    ├── researches/                 # index, create, edit, show, _form, _filters
    ├── community-services/  publications/  books/  intellectual-properties/
    ├── users/                      # Admin
    ├── verifications/              # Admin — antrean SUBMITTED lintas modul
    ├── settings/                   # Admin — unggah logo
    ├── master/{faculties,study-programs,schemes,funding-sources}/
    ├── reports/                    # Sprint 7
    └── vendor/pagination/          # paginasi Tailwind memakai token PMC
```

Folder view = nama route (`researches.index` → `views/researches/index.blade.php`).
Partial yang hanya dipakai satu modul diawali `_` di folder modulnya;
naikkan ke `components/shared/` saat modul kedua membutuhkannya.

## 2. Halaman

```blade
<x-layouts.app title="Penelitian">
    <x-shared.page-header title="Penelitian" description="Data penelitian dosen Universitas PMC" icon="heroicon-o-beaker">
        <x-slot:actions>
            @can('create', App\Models\Research::class)
                <x-ui.button :href="route('researches.create')" icon="heroicon-o-plus">Tambah Penelitian</x-ui.button>
            @endcan
        </x-slot:actions>
    </x-shared.page-header>

    @include('researches._filters')
    …
</x-layouts.app>
```

- Semua halaman berautentikasi dibungkus `<x-layouts.app>`; jangan tulis
  ulang sidebar/topbar.
- Create & Edit berbagi `_form.blade.php` (menerima model kosong saat
  create) — jangan duplikasi field.
- Breadcrumb: prop `:breadcrumbs="[['label' => 'Penelitian', 'url' => route('researches.index')], ['label' => 'Ubah']]"`
  — halaman index tidak mengirim breadcrumb.

## 3. Form

- `@csrf`, `@method('PUT')`, `enctype="multipart/form-data"` bila ada
  dokumen.
- Field lewat `<x-form.field>` (label + kontrol + error + teks bantu);
  kontrol mengisi `old()` sendiri:

```blade
<x-form.field name="title" label="Judul Penelitian" required class="md:col-span-2">
    <x-form.textarea name="title" rows="2" :value="$research->title" maxlength="500" required />
</x-form.field>
<x-form.field name="scheme_id" label="Jenis Skema" required>
    <x-form.select name="scheme_id" :options="$schemes" :value="$research->scheme_id" placeholder="Pilih skema" />
</x-form.field>
```

- **Penanda wajib:** `required` di `<x-form.field>` → bintang merah, hanya
  bila Form Request-nya `required` (`required_if` → bintang dinamis). Jangan
  tulis "(opsional)".
- Atribut HTML (`required`, `maxlength`, `min`, `max`, `accept`)
  mencerminkan Form Request; validasi sebenarnya tetap di server — tidak ada
  library validasi JS.
- Ada error → `<x-ui.alert tone="danger">Periksa kembali isian yang ditandai.</x-ui.alert>`
  di atas form + pesan per field.
- Tombol Simpan dinonaktifkan saat submit (`x-data="{ busy: false }" @submit="busy = true"`
  + `x-bind:disabled="busy"`).
- Form data kinerja (PRD §4.9): Dosen melihat **[Batal] [Simpan sebagai
  Draf] [Simpan & Ajukan]** — tombol terakhir `name="submit" value="1"`
  (primary); Admin hanya **[Batal] [Simpan]**. Tidak ada field `status`
  di form mana pun.

## 4. Tabel & daftar

- `<x-ui.table>` + `<x-shared.filter-bar>`; filter = form `GET` (`q`,
  `year`, `faculty`, `study_program`, atribut modul) → URL bisa dibagikan.
  Filter, urutan, paginasi **di server**.
- `{{ $researches->links() }}` (view `vendor/pagination` kustom) + info
  "Menampilkan 1–15 dari 120 data".
- Kolom anggota: ketua/penulis pertama + "dkk." — daftar lengkap di detail.
- Di bawah `md` tiap baris tampil sebagai kartu (`<ul class="md:hidden">`
  + `<table class="hidden md:table">`, satu partial baris aksi dipakai
  keduanya); tabel lebar dibungkus `overflow-x-auto`.
- Kosong → `<x-ui.empty-state>` (bedakan "belum ada data" vs "tidak cocok
  dengan filter" + tautan Reset).

## 5. Alpine.js & Tom Select

- Alpine untuk: sidebar HP, dropdown user, modal hapus, baris anggota
  dinamis, daftar file terpilih. Logika >10 baris → `resources/js/components/*.js`
  didaftarkan `Alpine.data('contributorsField', …)`.
- Tom Select hanya untuk memilih dosen (dicari nama/NIDN) di
  `contributors-field` dan filter dosen di Rekap; opsi dari props server,
  bukan endpoint AJAX (jumlah dosen kecil).
- Data server → JS hanya lewat `@js($value)`; jangan
  `{!! json_encode() !!}`.
- Tanpa jQuery, tanpa `onclick=""` inline, tanpa `confirm()` bawaan —
  hapus lewat `<x-shared.delete-button>` (modal).

## 6. Route, aset, ikon

- Selalu `route('researches.show', $research)`; jangan hardcode URL.
- `@vite(['resources/css/app.css', 'resources/js/app.js'])` hanya di layout.
- Ikon heroicons outline: `<x-heroicon-o-beaker class="size-5" />`;
  `size-4` inline/tombol, `size-5` nav/topbar. Komponen menerima nama ikon
  (`icon="heroicon-o-plus"`) dan merender `<x-dynamic-component>`.

## 7. Output & izin di view

- `{{ }}` saja; `{!! !!}` dilarang untuk data dari user.
- Tautan dokumen hanya `route('documents.download', $document)`.
- Tombol aksi dibungkus `@can('update', $model)` / `@can('delete', $model)`
  — jangan `@if(auth()->user()->role …)`.
- Tanggal: `$model->updated_at->translatedFormat('d F Y')` (locale `id`).

## 8. Build

- `npm run build` bersih sebelum commit.
- Tailwind v4 memindai `resources/views/**` lewat `@source`; class dinamis
  (`"bg-{$tone}-600"`) **dilarang** — pakai peta class lengkap
  (`['success' => 'bg-success/10 text-success-ink', …]`).
