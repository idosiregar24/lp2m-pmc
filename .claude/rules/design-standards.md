# Design Standards — Universitas PMC

Sumber visual: PRD §8. Gaya **modern, latar putih** mengikuti referensi
dashboard pilihan user (2026-10-10) — sidebar abu sangat muda, konten di
panel putih bersudut membulat, garis tipis, badge pastel — **dengan warna
instansi PMC** (poster resmi): merah untuk aksi utama & penanda aktif, emas
untuk aksen/logo, biru untuk info. Merah dipakai sebagai aksen, bukan latar
luas (tidak ada sidebar/halaman merah penuh).

## 1. Token — satu-satunya sumber warna

Semua warna ada di `resources/css/app.css` `@theme`. Jangan hex literal atau
warna default Tailwind (`bg-red-700`, `text-gray-500`) di view.

| Token | Nilai | Peran |
|---|---|---|
| `brand-600` / `-700` | `#B50606` / `#A50707` | **merah PMC** — tombol utama (+hover), ikon menu aktif, tab aktif, badge angka, halaman paginasi aktif, fokus |
| `brand-50/100/200` | merah lembut | tile ikon stat-card, avatar publik, border hover kartu |
| `gold-400` / `gold-800` | `#FFCE5F` / `#8A5A00` | **emas PMC** — teks logo, garis identitas login/publik, grafik; teks emas di atas putih pakai `gold-800` |
| `navy-700` | `#1F4C82` | **biru PMC** — info/"Diajukan", grafik |
| `primary-50/100/200` | abu netral | hover baris/menu, garis dekorasi |
| `canvas` | `#F7F7F8` | latar aplikasi di belakang panel, sidebar, tile ikon |
| `surface` | `#FFFFFF` | panel konten, kartu, input |
| `line` | `#E8E8EB` | semua border (hairline) |
| `ink` / `muted` | `#18181B` / `#71717A` | teks utama / sekunder |
| `{success,warning,danger,info,accent}` | solid | grafik, titik |
| `…-soft` / `…-line` / `…-ink` | pastel | latar / border / teks badge & alert |

| Kebutuhan | Class | JANGAN |
|---|---|---|
| Kartu/panel | `<x-ui.card>` (`rounded-2xl border border-line bg-surface`, tanpa bayangan) | `shadow-lg`, `bg-white` |
| Tombol utama | `<x-ui.button>` (merah PMC) | `bg-red-*` / hex |
| Tombol sekunder/outline | `variant="secondary"` / `"outline"` (putih, border tipis) | — |
| Hapus | `variant="outline-danger"` (teks merah lembut) / `"danger"` di modal | — |
| Link judul di tabel | `text-ink underline-offset-4 hover:underline` | link berwarna |
| Header tabel | huruf biasa `text-sm text-muted` (lihat `<x-ui.table>`) | `uppercase` |
| Fokus input | `focus:border-brand-200 focus:ring-4 focus:ring-brand-600/10` | ring tebal |

## 2. Komponen — pakai yang ada dulu

| Kebutuhan | Komponen |
|---|---|
| Judul halaman + deskripsi + tombol | `<x-shared.page-header title description>` + slot `actions`/`meta` (judul besar, tanpa tile ikon) |
| Panel berjudul | `<x-ui.card title description>` (slot `actions`, `footer`) |
| Angka ringkasan | `<x-ui.stat-card label value hint icon href>` |
| Tabel | `<x-ui.table>` + `<x-shared.filter-bar>` + `->links()` |
| Label + kontrol + error | `<x-form.field name label required hint>` |
| Pilihan master/enum/tahun | `<x-form.select :options>` |
| Anggota | `<x-shared.contributors-field>` / `<x-shared.contributors-list>` |
| Dokumen | `<x-shared.documents-field>` / `<x-shared.documents-list>` |
| Detail label–nilai | `<x-shared.detail-list>` + `<x-shared.detail-item>` |
| Status/kategori | `<x-ui.badge tone="success|warning|danger|info|accent|neutral">` |
| Tab | `<x-ui.tabs :items>` (garis bawah hitam) |
| Pesan | `<x-ui.alert>` |
| Data kosong | `<x-ui.empty-state>` |
| Konfirmasi hapus | `<x-shared.delete-button>` |
| Status + aksi + riwayat verifikasi | `<x-shared.verification-panel :record>` |
| Logo (atau placeholder) | `<x-ui.logo size="sm|md|lg">` |

Badge = pill pastel ber-border tipis (`border-*-line bg-*-soft text-*-ink`):

| Nilai | tone |
|---|---|
| Draf / Diajukan / Perlu Revisi / Terverifikasi | `neutral` / `info` (biru PMC) / `warning` (emas PMC) / `success` (`VerificationStatus::tone()`) |
| Aktif / Nonaktif | `success` / `neutral` |
| Admin / Dosen | `accent` (merah PMC lembut) / `info` |
| Internasional / Nasional | `accent` / `info` |
| Ketua / Penulis pertama | `warning` |
| Jenis KI, Jurnal/Prosiding, Luar PMC, nama modul | `neutral` |

Grafik (Chart.js — `resources/js/components/{barChart,donutChart,chartTokens}.js`)
membaca warna dari token: `brand-600`, `navy-700`, `gold-400`, `success`,
`muted` (status: `primary-200`, `navy-700`, `gold-400`, `success`).
**Dashboard dibuka dengan grid grafik:** tren per tahun (2 kolom) + donat
komposisi, lalu status per modul (2 kolom) + kartu aksi (antrean / perlu
revisi), baru kemudian stat-card dan tabel.

## 3. Tipografi & ikon

- Inter Variable self-hosted; tanpa CDN font.
- Judul halaman `text-2xl sm:text-3xl font-semibold tracking-tight`, judul
  kartu `text-base font-semibold`, isi `text-sm`. Tidak ada label
  `uppercase` (tabel, detail, sidebar).
- Heroicons outline, `size-4` di tombol, `size-[1.125rem]` di sidebar. Ikon
  modul: Dashboard `home`, Penelitian `beaker`, Pengabdian `user-group`,
  Publikasi `newspaper`, Buku `book-open`, HKI `light-bulb`, Pengguna
  `users`, Data Master `circle-stack`, Verifikasi `clipboard-document-check`,
  Rekap `chart-bar`, Pengaturan `cog-6-tooth`, Profil `user-circle`.

## 4. Layout

```
┌──────────────────┬──────────────────────────────────────────────┐
│ ┌──────────────┐ │ ╭──────────────────────────────────────────╮ │
│ │(PMC) BP2M    │ │ │ Beranda > Penelitian                     │ │
│ └──────────────┘ │ │ Penelitian            [Ekspor] [■ Tambah]│ │
│  ⌂ Dashboard     │ │ deskripsi                                │ │
│ ──────────────   │ │ ┌─ card ────────────────────────────────┐│ │
│ Data Kinerja     │ │ │ filter-bar                            ││ │
│ ┌Penelitian────┐ │ │ │ tabel (header huruf biasa)            ││ │
│ └──────────────┘ │ │ └───────────────────────────────────────┘│ │
│  Pengabdian …    │ ╰──────────────────────────────────────────╯ │
│ (AB) Admin   ⇅   │          panel putih di atas canvas abu      │
└──────────────────┴──────────────────────────────────────────────┘
```

- **Sidebar** (`bg-canvas`, tanpa border di desktop): kartu identitas putih
  (logo + "BP2M / Universitas PMC") di atas; label grup `text-xs text-muted`
  (huruf biasa) dipisah garis tipis; item `text-ink/75`, ikon `text-muted`;
  **item aktif = kartu putih** `border-line bg-surface shadow-xs font-medium`
  dengan ikon merah PMC. Angka antrean Verifikasi = pill merah kecil. Menu pengguna (avatar,
  nama, role → Profil Saya, Keluar) di bawah sidebar. Item tanpa route →
  teks pudar + pill "Segera". Di bawah `lg` sidebar jadi panel geser.
- **Konten:** desktop = panel `bg-surface rounded-2xl border border-line`
  dengan jarak 8px dari tepi; HP = putih penuh dengan bar atas (hamburger).
  Breadcrumb teks kecil di atas judul ("Beranda > …").
- **Login & portal publik:** latar putih, garis identitas tipis di atas
  (`from-brand-600 via-brand-700 to-gold-400`), kartu `rounded-2xl border`,
  tombol merah. Portal publik (`<x-layouts.public>`): header logo + "Cari
  Dosen" + Masuk.
- **Logo:** diunggah Admin (PRD §4.12) lewat `<x-ui.logo>`; belum ada logo →
  tile merah PMC membulat berisi "PMC" emas.
- **Status verifikasi di detail:** kartu "Status Verifikasi" di kolom kanan
  (badge, catatan revisi `<x-ui.alert tone="warning">`, tombol aksi `@can`,
  riwayat). Data terkunci untuk Dosen → tombol Ubah/Hapus tidak dirender +
  `<x-ui.alert tone="info">`.

## 5. Bahasa

Semua teks UI Bahasa Indonesia: Tambah, Simpan, Batal, Ubah, Hapus, Unduh,
Lihat, Cari…, Semua tahun, Terapkan, Reset. Istilah: Penelitian,
Pengabdian kepada Masyarakat ("Pengabdian" di menu), Publikasi, Buku, HKI,
NIDN/NUPTK, Fakultas, Program Studi ("Prodi" di tabel), Skema, Sumber Dana,
Mitra, Dokumen Pendukung, Luar PMC.
