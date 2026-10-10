# Rencana Sprint — Sistem Informasi BP2M Universitas PMC

Sumber kebutuhan: [`PRD-BP2M-PMC.md`](../File%20Skema/BP2M%20v1.0.0/PRD-BP2M-PMC.md).
Setiap sprint = satu file checklist. Centang butir begitu selesai **dan**
teruji (Definition of Done PRD §10), lalu perbarui tabel status di bawah.

## Status

| Sprint | Isi | Status |
|---|---|---|
| [00](sprint-00-setup.md) | Instalasi Laravel, Tailwind v4 + token PMC, layout & komponen dasar | 🟨 Laravel terpasang; tampilan belum |
| [01](sprint-01-auth-profil.md) | Login username, middleware, ganti password, Profil Saya | ⬜ Belum mulai |
| [02](sprint-02-user-master.md) | Kelola User + Data Master + Pengaturan (unggah logo) | ⬜ Belum mulai |
| [03](sprint-03-penelitian.md) | Fondasi bersama (anggota, dokumen, **verifikasi**, Policy, filter) + **Penelitian** (modul acuan) | ⬜ Belum mulai |
| [04](sprint-04-pengabdian.md) | Pengabdian kepada Masyarakat | ⬜ Belum mulai |
| [05](sprint-05-publikasi-buku.md) | Publikasi + Buku | ⬜ Belum mulai |
| [06](sprint-06-hki.md) | HKI | ⬜ Belum mulai |
| [07](sprint-07-dashboard-rekap.md) | Dashboard, Rekap, Ekspor Excel, hardening & UAT | ⬜ Belum mulai |

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

## Catatan penyesuaian

- Skema database = PRD §5 (tidak ada file SQL terpisah).
- Warna diambil dari poster "Launching Universitas PMC" — lihat
  `rules/design-standards.md`.
