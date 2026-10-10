# Sprint 07 — Dashboard, Rekap, Ekspor & Hardening

PRD §4.10, §9, §10. Semua angka memakai `visibleTo($user)` — Dosen hanya
melihat hitungan datanya sendiri — dan **`verified()`** (angka resmi =
Terverifikasi saja), kecuali kartu status/antrean.

## Dashboard

- [x] Admin: kartu "Menunggu verifikasi" (jumlah SUBMITTED → link
      `verifications.index`), 5 `stat-card` (tahun berjalan besar + "Total: n"), grafik
      jumlah per tahun 5 tahun terakhir per modul (baca skill `dataviz`
      dulu; Chart.js via npm, warna dibaca dari token CSS), tabel rekap per
      fakultas.
- [x] Dosen: daftar "Perlu revisi" (dengan catatan) & "Draf belum
      diajukan", 5 `stat-card` data miliknya yang Terverifikasi, "Data
      terbaru saya" (5 entri lintas modul, tautan ke detail).
- [x] Query hitungan diringkas (satu `count` per modul, bukan per baris).

## Rekap (Admin)

- [x] `ReportController` → `reports.index`: tabel per dosen (jumlah per
      modul), filter tahun / fakultas / prodi / dosen (Tom Select),
      paginasi; klik angka → daftar modul tersaring dosen itu.
- [x] Navigasi: Rekap → `reports.index`.

## Ekspor Excel

- [x] `composer require maatwebsite/excel`.
- [x] `app/Exports/{Model}Export` per modul: kolom = field PRD §4.x +
      anggota (dipisah "; ", ketua/penulis pertama di depan) + fakultas/prodi
      ketua + jumlah dokumen + status; default hanya Terverifikasi (ikuti
      filter status bila diisi); header Bahasa Indonesia; cegah formula
      injection.
- [x] Tombol "Ekspor Excel" di setiap halaman daftar, membawa filter aktif;
      route `{module}.export` memakai `visibleTo` (Dosen = datanya sendiri).
- [x] Ekspor Rekap (Admin).

## Hardening & UAT

- [x] `/security-review` atas seluruh diff; tindak lanjuti temuan.
- [ ] Coverage ≥70% (`php artisan test --coverage`).
- [x] Cek N+1 di semua index (Laravel Debugbar/`preventLazyLoading` di
      lokal).
- [x] Uji tampilan 375px / 768px / 1280px semua halaman.
- [x] `ProductionSeeder` + panduan deploy singkat (`README.md` proyek):
      `.env` production, `APP_DEBUG=false`, HTTPS, `storage` permission,
      backup database & `storage/app/private`.
- [ ] Sesi UAT dengan BP2M; catat perubahan ke PRD §11 → sprint baru.
