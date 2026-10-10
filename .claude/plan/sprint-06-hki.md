# Sprint 06 — HKI (Hak Kekayaan Intelektual)

PRD §4.8. Salin bentuk Penelitian.

- [ ] Enum `IntellectualPropertyType` (9 jenis PRD §4.8, `label()`
      Indonesia: Hak Cipta, Paten, Paten Sederhana, Merek, Desain Industri,
      Desain Tata Letak Sirkuit Terpadu, Rahasia Dagang, Indikasi
      Geografis, Perlindungan Varietas Tanaman).
- [ ] Migrasi `intellectual_properties` (PRD §5.3, kolom verifikasi; model `HasVerification`).
- [ ] Alias `intellectual_property` di `Verifiable`; form & detail memakai tombol/panel verifikasi.
- [ ] Model `IntellectualProperty` + factory; `scopeFilter()` (q, year,
      faculty, study_program, type).
- [ ] Form Request + `IntellectualPropertyService` +
      `IntellectualPropertyPolicy`.
- [ ] Form: label anggota "Pemilik KI"; pihak luar boleh berupa institusi
      (mis. "Universitas PMC") — field afiliasi opsional.
- [ ] `IntellectualPropertyController`; route `/hki` →
      `intellectual-properties.*`.
- [ ] View `intellectual-properties/` (badge jenis KI).
- [ ] Navigasi: HKI → `intellectual-properties.index`.
- [ ] Detail user: jumlah HKI. `DemoDataSeeder`: 10 HKI.
- [ ] `UserPolicy::delete` kini memeriksa kelima modul (pembuat & anggota).
- [ ] Test lengkap (`backend-standards.md` §6).
