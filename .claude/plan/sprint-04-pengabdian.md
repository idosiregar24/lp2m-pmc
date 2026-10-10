# Sprint 04 — Pengabdian kepada Masyarakat

PRD §4.5. Salin bentuk modul Penelitian (skill `blade-module`).

- [ ] Migrasi `community_services` (PRD §5.3: + `partner` 255, kolom verifikasi).
- [ ] Model `CommunityService` (`HasContributors`, `HasDocuments`, `HasVerification`,
      `SoftDeletes`) + factory; `scopeFilter()` (q juga mencari `partner`,
      year, faculty, study_program, scheme, funding_source).
- [ ] Form Request: skema tipe `COMMUNITY_SERVICE`; `partner` wajib maks 255.
- [ ] `CommunityServiceService`, `CommunityServicePolicy extends ActivityPolicy`.
- [ ] `CommunityServiceController`; route `/pengabdian` → `community-services.*`.
- [ ] View `community-services/` (label anggota "Pengabdi", kolom Mitra di
      tabel).
- [ ] Navigasi: Pengabdian → `community-services.index`.
- [ ] Alias `community_service` di `Verifiable` → muncul di antrean Verifikasi; form & detail memakai tombol/panel verifikasi.
- [ ] Detail user: jumlah pengabdian. `DemoDataSeeder`: 20 pengabdian.
- [ ] Test lengkap (`backend-standards.md` §6) + skema Penelitian ditolak di
      form Pengabdian.
