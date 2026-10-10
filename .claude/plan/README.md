# Rencana Sprint — Sistem Informasi BP2M Universitas PMC

Sumber kebutuhan: [`PRD-BP2M-PMC.md`](../File%20Skema/BP2M%20v1.0.0/PRD-BP2M-PMC.md).
Setiap sprint = satu file checklist. Centang butir begitu selesai **dan**
teruji (Definition of Done PRD §10), lalu perbarui tabel status di bawah.

## Status

| Sprint | Isi | Status |
|---|---|---|
| [00](sprint-00-setup.md) | Instalasi Laravel, Tailwind v4 + token PMC, layout & komponen dasar | ✅ Selesai |
| [01](sprint-01-auth-profil.md) | Login username, middleware, ganti password, Profil Saya | ✅ Selesai |
| [02](sprint-02-user-master.md) | Kelola User + Data Master + Pengaturan (unggah logo) | ✅ Selesai |
| [03](sprint-03-penelitian.md) | Fondasi bersama (anggota, dokumen, **verifikasi**, Policy, filter) + **Penelitian** (modul acuan) | ✅ Selesai |
| [04](sprint-04-pengabdian.md) | Pengabdian kepada Masyarakat | ✅ Selesai |
| [05](sprint-05-publikasi-buku.md) | Publikasi + Buku | ✅ Selesai |
| [06](sprint-06-hki.md) | HKI | ✅ Selesai |
| [07](sprint-07-dashboard-rekap.md) | Dashboard, Rekap, Ekspor Excel, hardening & UAT | 🟨 Tinggal UAT & ukur coverage |

Legenda: ⬜ belum · 🟨 berjalan · ✅ selesai.

## Urutan & ketergantungan

```
00 Setup → 01 Auth → 02 User & Master → 03 Penelitian (+fondasi)
                                          ├→ 04 Pengabdian
                                          ├→ 05 Publikasi & Buku
                                          └→ 06 HKI
                                                    └→ 07 Dashboard & Rekap
```

Sprint 04–06 menyalin bentuk Penelitian (skill `blade-module`) dan boleh
dikerjakan dalam urutan apa pun setelah 03 selesai.

## Keputusan (PRD §11)

Sudah diputuskan user 2026-10-10:

1. Dosen menginput datanya sendiri — **ya**.
2. Verifikasi Admin — **ya** (Draf → Diajukan → Terverifikasi / Perlu
   Revisi) → dibangun di Sprint 03, dipakai Sprint 04–07.
3. Role Pimpinan — **tidak untuk sekarang** (hanya ADMIN & DOSEN).
4. Logo — **diunggah BP2M lewat menu Pengaturan** → Sprint 02.
5. Daftar resmi fakultas/prodi/skema/sumber dana — **menyusul**; pakai
   data contoh, Admin mengisi lewat Data Master.

Masih terbuka (PRD §11.2, tidak menghambat sprint): kepanjangan BP2M,
siapa boleh ubah data bersama, wajib dokumen saat mengajukan, field
tambahan (DOI/ISBN/nomor HKI/dana), format selain PDF, server produksi.
Bila jawabannya berubah, perbarui PRD dulu, lalu sprint yang terdampak.

## Git

Repo `https://github.com/idosiregar24/bp2m-pmc` (branch `main`). User yang
melakukan commit & push — Claude hanya menyiapkan pesan commit di akhir
setiap pekerjaan.

## Catatan implementasi Sprint 00–03 (2026-10-10)

- `App\Support\Verifiable::MODULES` adalah registri modul data: morph map,
  route verifikasi `{type}/{id}`, antrean Verifikasi, dashboard, ringkasan
  user, dan `User::hasActivityData()` membacanya. Sprint 04–06 cukup menambah
  satu entri di sana (+ model/policy/controller/view modulnya).
- `Scheme::isInUse()` / `FundingSource::isInUse()` masih memeriksa
  `researches` saja — tambahkan `community_services` di Sprint 04.
- Navigasi otomatis aktif begitu route `index` ada (`Route::has()`), tanpa
  edit manual.
- Data Master memakai satu `Save{Model}Request` + satu `form.blade.php` per
  master (form kecil, create = edit).
- Prodi unik per (fakultas, nama, **jenjang**) — D3 dan S1 Farmasi boleh
  sama-sama ada.
- Tombol "Simpan & Ajukan" mengirim niat lewat hidden input `submit`
  (`x-ref`), bukan `name/value` tombol — tombol yang di-`disabled` saat submit
  tidak ikut terkirim.
- Pola wajib modul berikutnya (hasil review keamanan Sprint 03):
  - `{Model}Service::update()` memanggil
    `VerificationService::ensureEditableByOwner()` di awal transaksi untuk
    Dosen (cek ulang status dengan row lock).
  - `Update{Model}Request` meng-override `currentContributorIds()` agar
    anggota yang kini Admin/nonaktif tetap valid.
  - Filter daftar dibaca lewat `$this->filters($request, [...])` (base
    `Controller`), bukan `$request->only()` / `request('q')` di view.

## Catatan implementasi Sprint 04–07 (2026-10-10)

- Bagian yang sama di kelima modul dipindah ke kelas bersama:
  `ActivityService` (simpan + anggota + dokumen + verifikasi),
  `Requests\Concerns\UpdatesActivity` (authorize/kuota dokumen/anggota lama),
  `ValidatesFunding` (skema per jenis + sumber dana), factory trait
  `ActivityStates`, `Exports\ActivityExport` (kolom bersama + anti formula
  injection), `Controllers\Concerns\ExportsActivity`.
- Filter `lecturer` (anggota mana pun) di kelima daftar = tujuan klik angka
  Rekap; tampil sebagai chip di `filter-bar`.
- Dashboard: satu query `groupBy(year)` per modul; total, tahun berjalan
  dan grafik 5 tahun diturunkan dari hasil itu. Rekap fakultas menghitung
  satu data di setiap fakultas anggotanya.
- `maatwebsite/excel` terpasang **v4** (API concern sama dengan 3.1);
  grafik `chart.js` v4 (komponen Alpine `yearlyChart`, warna dari token CSS).
- Coverage belum terukur: PHP lokal tidak punya PCOV/Xdebug. Pasang
  `php_pcov` di Laragon lalu `php artisan test --coverage`.

## Catatan penyesuaian

- Skema database = PRD §5 (tidak ada file SQL terpisah).
- Tema visual 2026-10-10 (permintaan user): layout modern putih seperti
  referensi, tetap dengan warna instansi PMC — lihat `rules/design-standards.md`.
- Tambahan di luar sprint (2026-10-10): dashboard dibuka dengan grid grafik
  (tren, komposisi, status) dan **portal publik** `/publik` (PRD §4.13).
