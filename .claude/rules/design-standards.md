# Design Standards — Universitas PMC

Sumber visual: PRD §8. Gaya **sederhana, bersih, formal-akademik**. Warna
diambil langsung dari poster resmi "Launching Universitas PMC" (disampling
dari piksel poster):

| Elemen poster | Hex | Peran di sistem |
|---|---|---|
| Merah pusat latar | `#B50606` | **Primer** — tombol utama, link aktif, fokus |
| Merah tengah | `#A50707` | hover tombol primer |
| Merah tepi | `#7A0D0D` | sidebar |
| Merah paling gelap | `#421212` | ujung gradasi sidebar / login |
| Emas judul & garis tepi | `#FFCE5F` | **Aksen** — hanya di atas merah/gelap |
| Biru panel samping | `#1F4C82` | **Sekunder** — tombol sekunder, info, level Nasional |
| Putih logo-pill | `#FFFFFF` | kartu/panel |

Merah = identitas & aksi utama, emas = aksen tipis, biru = pendukung, sisa
layar putih & abu hangat. Jangan membuat halaman "merah semua" — merah
pekat hanya di sidebar, halaman login, dan tombol primer.

## 1. Token — satu-satunya sumber warna

```css
/* resources/css/app.css */
@import 'tailwindcss';
@import '@fontsource-variable/inter';
@source '../views';
@source '../js';

@theme {
  --font-sans: 'Inter Variable', ui-sans-serif, system-ui, sans-serif;

  /* brand = merah poster */
  --color-brand-50:  #FDF2F2;
  --color-brand-100: #FBE2E2;
  --color-brand-200: #F5BDBD;
  --color-brand-500: #D11F1F;
  --color-brand-600: #B50606;   /* primer */
  --color-brand-700: #A50707;   /* hover */
  --color-brand-800: #7A0D0D;   /* sidebar */
  --color-brand-900: #5C0F0F;
  --color-brand-950: #421212;   /* gradasi gelap */

  /* gold = emas poster (aksen, JANGAN untuk teks di atas putih) */
  --color-gold-50:  #FFF8E6;
  --color-gold-100: #FFEFC7;
  --color-gold-400: #FFCE5F;    /* emas poster */
  --color-gold-800: #8A5A00;    /* teks bernuansa emas di atas putih (5.9:1) */

  /* navy = biru poster (sekunder) */
  --color-navy-50:  #EEF3F9;
  --color-navy-100: #DCE6F2;
  --color-navy-700: #1F4C82;    /* biru poster */
  --color-navy-800: #193E6A;

  /* netral */
  --color-canvas:  #F6F5F3;     /* latar halaman */
  --color-surface: #FFFFFF;     /* kartu, panel, topbar */
  --color-line:    #E4E2DE;     /* border */
  --color-ink:     #1C1917;     /* teks utama */
  --color-muted:   #6B6560;     /* teks sekunder */

  /* status: latar = warna/10, teks = *-ink */
  --color-success: #16A34A;  --color-success-ink: #166534;
  --color-warning: #F59E0B;  --color-warning-ink: #92400E;
  --color-danger:  #DC2626;  --color-danger-ink:  #991B1B;
  --color-info:    #1F4C82;  --color-info-ink:    #1F4C82;
}
```

| Kebutuhan | Class | JANGAN |
|---|---|---|
| Latar halaman | `bg-canvas` | `bg-gray-100` |
| Kartu/panel | `bg-surface border border-line rounded-xl` | `bg-white border-gray-200` |
| Teks utama / sekunder | `text-ink` / `text-muted` | `text-gray-900` / `text-gray-500` |
| Tombol primer | `<x-ui.button>` (`bg-brand-600 hover:bg-brand-700 text-white`) | `bg-red-700`, `bg-[#B50606]` |
| Tombol sekunder | `<x-ui.button variant="secondary">` (`border-navy-700 text-navy-700 hover:bg-navy-50`) | — |
| Tombol samar | `<x-ui.button variant="ghost">` (`text-muted hover:bg-canvas`) | — |
| Hapus | `<x-ui.button variant="danger">` | — |
| Aksen emas | `bg-gold-400 text-brand-950` / `text-gold-400` **di atas** `brand-800+` | `text-gold-400` di atas putih (±1.5:1) |
| Header tabel | `bg-gold-50 text-muted uppercase text-xs` | — |
| Fokus | `focus-visible:ring-2 focus-visible:ring-brand-600 focus-visible:ring-offset-2` | outline default dihapus tanpa ganti |

Kontras sudah dicek: putih di atas `brand-600` 7.0:1, `gold-400` di atas
`brand-800` 7.5:1, `navy-700` di atas putih 8.7:1, `gold-800` di atas putih
5.9:1.

## 2. Komponen — pakai yang ada dulu

| Kebutuhan | Komponen |
|---|---|
| Judul halaman + deskripsi + tombol | `<x-shared.page-header title description icon>` + slot `actions` |
| Panel berjudul | `<x-ui.card title description>` (slot `actions`, `footer`) |
| Angka ringkasan | `<x-ui.stat-card label value hint icon href>` |
| Tabel | `<x-ui.table>` + `<x-shared.filter-bar>` + `->links()` |
| Label + kontrol + error | `<x-form.field name label required hint>` |
| Pilihan master/enum/tahun | `<x-form.select :options>` |
| Anggota (peneliti, penulis, pemilik KI) | `<x-shared.contributors-field :label :lecturers :value>` / `<x-shared.contributors-list>` |
| Dokumen | `<x-shared.documents-field :existing>` / `<x-shared.documents-list>` |
| Detail label–nilai | `<x-shared.detail-list>` |
| Status/kategori | `<x-ui.badge tone="success|warning|danger|info|neutral|brand">` |
| Tab (Data Master) | `<x-ui.tabs :items>` (tiap tab = link route) |
| Pesan sukses/gagal | `<x-ui.alert>` — flash `session('success'|'error')` dirender layout |
| Data kosong | `<x-ui.empty-state title description icon>` |
| Konfirmasi hapus | `<x-shared.delete-button :action :name>` |
| Status + aksi + riwayat verifikasi | `<x-shared.verification-panel :record>` |
| Logo (atau placeholder) | `<x-ui.logo size="sm|md|lg">` |

Badge = pill polos ber-tint, tanpa titik/border/glow:

| Nilai | tone |
|---|---|
| Draf / Diajukan / Perlu Revisi / Terverifikasi | `neutral` / `info` / `warning` / `success` (dari `VerificationStatus::tone()`) |
| Aktif / Nonaktif | `success` / `neutral` |
| Admin / Dosen | `brand` / `info` |
| Internasional / Nasional | `brand` / `info` |
| Jurnal / Prosiding | `neutral` |
| Ketua / Penulis pertama | `warning` |
| Luar PMC | `neutral` |
| Jenis KI | `neutral` |

## 3. Tipografi & ikon

- Inter Variable self-hosted; tidak ada `<link>` Google/Bunny Fonts, tidak
  ada CDN.
- Judul halaman `text-xl font-semibold text-ink`, judul kartu
  `text-base font-semibold`, isi `text-sm`, label tabel `text-xs uppercase`.
- Heroicons outline. Ikon modul (sama di sidebar, page-header, stat-card):
  Dashboard `home`, Penelitian `beaker`, Pengabdian `user-group`, Publikasi
  `newspaper`, Buku `book-open`, HKI `light-bulb`, Pengguna `users`, Data
  Master `circle-stack`, Verifikasi `clipboard-document-check`, Rekap
  `chart-bar`, Pengaturan `cog-6-tooth`, Profil `user-circle`.

## 4. Layout

```
┌───────────────────┬─────────────────────────────────────────────┐
│ (logo) BP2M       │ breadcrumb                    Nama ▾ [Dosen] │ topbar bg-surface border-b
│ Universitas PMC   ├─────────────────────────────────────────────┤
│ ───────────────── │ page-header                                 │
│ ⌂ Dashboard       │ ┌──────────── card ─────────────┐           │
│ DATA KINERJA      │ │ filter-bar                    │ bg-canvas │
│ ▌Penelitian       │ │ table / kartu HP              │           │
│  Pengabdian       │ │ paginasi                      │           │
│  Publikasi        │ └───────────────────────────────┘           │
│  Buku · HKI       │                                             │
│ ADMINISTRASI      │                                             │
│  Verifikasi  (3)  │                                             │
│  Pengguna         │                                             │
│  Data Master      │                                             │
│  Rekap            │                                             │
│  Pengaturan       │                                             │
└───────────────────┴─────────────────────────────────────────────┘
```

- **Sidebar:** `bg-linear-to-b from-brand-800 to-brand-950`, garis tepi
  kanan tipis `border-gold-400/40`, teks `text-white/80`, label grup
  `text-gold-400/80 text-xs uppercase`. Item aktif: `bg-white/10` + garis
  kiri `border-l-4 border-gold-400` + teks `text-gold-400`. Item tanpa route
  → `opacity-50` + badge "Segera". Dibangun dari `App\Support\Navigation`;
  item Admin tidak dirender untuk Dosen. Menu Verifikasi menampilkan
  angka antrean (jumlah SUBMITTED) sebagai pill `bg-gold-400 text-brand-950`
  — dihitung sekali per request di `Navigation`, bukan di view.
- **Topbar:** putih, breadcrumb kiri, kanan menu pengguna (nama, badge role,
  Profil Saya, Keluar). Di bawah `lg` sidebar jadi panel geser (tombol
  hamburger, Alpine, `x-trap`).
- **Konten:** `bg-canvas`, `max-w-7xl mx-auto p-4 lg:p-6`, kartu putih.
- **Login (`<x-layouts.guest>`):** latar gradasi poster
  `from-brand-600 via-brand-800 to-brand-950` dengan bingkai tipis emas di
  sisi (mengingatkan garis tepi poster), kartu putih di tengah: logo,
  "Sistem Informasi BP2M", "Universitas PMC", field Username + Password,
  tombol primer lebar penuh.
- **Logo:** diunggah Admin di menu Pengaturan (PRD §4.12), dibaca lewat
  `App\Support\Settings::logoUrl()` oleh komponen `<x-ui.logo size>` —
  dipakai sidebar, login, favicon. Belum ada logo → lingkaran `bg-gold-400`
  berisi teks "PMC" `text-brand-950`. Jangan taruh file logo di repo.
- **Status verifikasi di halaman detail:** kartu "Status Verifikasi" di
  kolom kanan (di atas Dokumen): badge status, catatan revisi terakhir
  dalam `<x-ui.alert tone="warning">` bila `NEEDS_REVISION`, tombol aksi
  sesuai `@can` (Dosen: Ajukan; Admin: Verifikasi · Kembalikan · Buka
  Kembali — Kembalikan/Buka Kembali membuka modal berisi textarea catatan
  wajib), lalu riwayat (timeline kecil: tanggal, aktor, dari → ke, catatan).
- **Data terkunci:** saat Dosen membuka data SUBMITTED/VERIFIED, tombol
  Ubah/Hapus tidak dirender dan muncul `<x-ui.alert tone="info">` "Data
  sedang diverifikasi / sudah terverifikasi — hubungi admin BP2M bila perlu
  perubahan."

## 5. Bahasa

Semua teks UI Bahasa Indonesia: Tambah, Simpan, Batal, Ubah, Hapus, Unduh,
Lihat, Cari…, Semua tahun, Terapkan, Reset. Istilah: Penelitian,
Pengabdian kepada Masyarakat ("Pengabdian" di menu), Publikasi, Buku, HKI,
NIDN/NUPTK, Fakultas, Program Studi ("Prodi" di tabel), Skema, Sumber Dana,
Mitra, Dokumen Pendukung, Luar PMC.
