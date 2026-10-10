---
name: front-end-design
description: Use when building or restyling any BP2M PMC UI in Blade — a new page, form, table, detail page, dashboard widget, badge, or layout work in resources/views or resources/css. Applies the BP2M design system (PRD §8, Tailwind v4 tokens, shared Blade components) consistently instead of improvising colors and spacing per page.
---

# Front-End Design — BP2M Universitas PMC

Pendamping praktis untuk `.claude/rules/design-standards.md` (token &
komponen) dan `.claude/rules/frontend-standards.md` (struktur kode).
Baca saat akan menulis/menyentuh tampilan.

## Sebelum menulis markup

1. **Buka `resources/css/app.css`**, bukan mengandalkan ingatan — lihat
   token yang benar-benar ada (`brand-*` merah PMC untuk aksi & penanda
   aktif, `gold-*` aksen, `navy-*` info, `primary-*` abu netral untuk hover,
   `canvas`, `surface`, `ink`, `muted`, `line`, status
   `{success,warning,danger,info,accent}` + `-soft`/`-line`/`-ink`). Layout
   modern putih, merah hanya sebagai aksen; lihat `rules/design-standards.md`.
2. **Jangan pernah** hex atau warna default Tailwind. Token belum ada →
   tambahkan di `@theme`.
3. Pilih kerangka: halaman internal → `<x-layouts.app>`; login/ganti
   password paksa → `<x-layouts.guest>`.
4. Cari halaman saudara yang sudah jadi (biasanya `researches/`) dan salin
   anatominya.

## Anatomi halaman daftar

```
page-header (judul, deskripsi singkat, ikon menu, tombol "Tambah …")
filter-bar  (cari · tahun · fakultas → prodi · atribut modul · status · [Terapkan] [Reset])
ui.table    (Judul | Tahun | Ketua/Penulis | atribut | Dokumen | Status | aksi)
            HP: daftar kartu (judul, tahun · badge atribut · badge status, ketua, aksi)
paginasi    (links(), info "Menampilkan 1–15 dari 120 data")
```

- Kolom aksi: ikon Lihat / Ubah / Hapus (`@can`), rata kanan.
- Kosong: `empty-state` dengan ikon modul, "Belum ada data penelitian."
  + tombol Tambah (bila `@can`); kosong karena filter → "Tidak ada data
  yang cocok dengan filter." + tautan Reset.
- Judul data adalah link ke halaman detail.

## Anatomi form

- Satu `ui.card` per kelompok: **Informasi Utama** (judul, tahun, skema,
  sumber dana / level / jenis…), **Peneliti/Pengabdi/Penulis/Pemilik KI**
  (`contributors-field`), **Dokumen Pendukung** (`documents-field`).
- Grid `md:grid-cols-2`; judul dan anggota selebar penuh.
- `contributors-field`: tiap baris = toggle "Dosen PMC / Luar PMC" →
  Tom Select dosen **atau** input nama + afiliasi; tombol naik/turun
  urutan; baris pertama berlabel "Ketua" (penelitian/pengabdian) atau
  "Penulis pertama"; tombol "+ Tambah anggota".
- `documents-field`: input file multiple PDF + teks bantu "PDF, maks
  10 MB per file, maks 10 file."; di Edit tampil daftar dokumen lama
  dengan tombol hapus (konfirmasi).
- Footer Dosen: [Batal] (ghost) [Simpan sebagai Draf] (secondary)
  [Simpan & Ajukan] (primary); Admin: [Batal] [Simpan]. Tombol
  `x-bind:disabled` saat submit untuk cegah klik ganda. Data
  `NEEDS_REVISION`: catatan revisi tampil di atas form
  (`alert tone="warning"`).
- Error validasi: pesan di bawah field + `alert` ringkas di atas form
  "Periksa kembali isian yang ditandai."

## Anatomi detail

- Header: judul data (bukan "Detail Penelitian"), sub: tahun · badge
  atribut; aksi Ubah / Hapus.
- `lg`: kiri 2/3 `detail-list` + daftar anggota (nama, prodi, badge
  "Luar PMC", penanda Ketua); kanan 1/3 `verification-panel` (status,
  catatan revisi, tombol Ajukan / Verifikasi · Kembalikan · Buka Kembali,
  riwayat) lalu kartu Dokumen (nama file, ukuran, tombol Unduh). HP:
  bertumpuk, panel verifikasi paling atas.
- Data terkunci untuk Dosen (Diajukan/Terverifikasi): tanpa tombol
  Ubah/Hapus + alert info (lihat `design-standards.md` §4).
- Footer kecil `text-muted`: "Dibuat oleh … pada …, diperbarui …".

## Dashboard

- Baris `stat-card` (Penelitian, Pengabdian, Publikasi, Buku, HKI):
  angka tahun berjalan besar + "Total: n" kecil.
- Grafik (Sprint 7): baca skill `dataviz` dulu; library ringan via npm
  (mis. Chart.js), warna dari token CSS (`getComputedStyle`), bukan hex.
- Angka modul = **Terverifikasi saja**; Admin punya kartu pertama
  "Menunggu verifikasi" (link ke `verifications.index`).
- Dosen: kartu "Perlu revisi" & "Draf belum diajukan" (paling atas bila
  ada isinya), lalu "Data terbaru saya" berisi 5 entri lintas modul.

## Responsif & cek akhir

1. Cek di 375px, 768px, 1280px — tidak ada scroll horizontal halaman
   (tabel boleh scroll di dalam wrapper-nya).
2. Target sentuh ≥40px di HP; tombol utama form lebar penuh di HP.
3. Fokus keyboard terlihat (`focus-visible:ring-2 ring-brand-600`).
4. Semua teks Bahasa Indonesia; tanggal `translatedFormat`.
5. `grep` hex/warna default di file yang disentuh → harus kosong.
6. `npm run build` bersih.

## Jangan

- Membuat ulang sidebar/topbar, kartu, tombol dengan `div` + class mentah.
- Menaruh logika izin hanya di view (`@if(auth()->user()->role…)`) —
  pakai `@can` yang membaca Policy.
- Menambah library UI (Bootstrap, Flowbite, DaisyUI) — semua dari
  komponen sendiri + Tailwind.
- Memuat aset dari CDN.
