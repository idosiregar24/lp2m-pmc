# Sprint 04 — Pengabdian kepada Masyarakat

PRD §4.5. Salin bentuk modul Penelitian (skill `blade-module`).

- [x] Migrasi `community_services` (PRD §5.3: + `partner` 255, kolom verifikasi).
- [x] Model `CommunityService` (`HasContributors`, `HasDocuments`, `HasVerification`,
      `SoftDeletes`) + factory; `scopeFilter()` (q juga mencari `partner`,
      year, faculty, study_program, scheme, funding_source).
- [x] Form Request: skema tipe `COMMUNITY_SERVICE`; `partner` wajib maks 255.
- [x] `CommunityServiceService`, `CommunityServicePolicy extends ActivityPolicy`.
- [x] `CommunityServiceController`; route `/pengabdian` → `community-services.*`.
- [x] View `community-services/` (label anggota "Pengabdi", kolom Mitra di
      tabel).
- [x] Navigasi: Pengabdian → `community-services.index`.
- [x] Alias `community_service` di `Verifiable` → muncul di antrean Verifikasi; form & detail memakai tombol/panel verifikasi.
- [x] Detail user: jumlah pengabdian. `DemoDataSeeder`: 20 pengabdian.
- [x] Test lengkap (`backend-standards.md` §6) + skema Penelitian ditolak di
      form Pengabdian.
