# Sprint 06 — HKI (Hak Kekayaan Intelektual)

PRD §4.8. Salin bentuk Penelitian.

- [x] Enum `IntellectualPropertyType` (9 jenis PRD §4.8, `label()`
      Indonesia: Hak Cipta, Paten, Paten Sederhana, Merek, Desain Industri,
      Desain Tata Letak Sirkuit Terpadu, Rahasia Dagang, Indikasi
      Geografis, Perlindungan Varietas Tanaman).
- [x] Migrasi `intellectual_properties` (PRD §5.3, kolom verifikasi; model `HasVerification`).
- [x] Alias `intellectual_property` di `Verifiable`; form & detail memakai tombol/panel verifikasi.
- [x] Model `IntellectualProperty` + factory; `scopeFilter()` (q, year,
      faculty, study_program, type).
- [x] Form Request + `IntellectualPropertyService` +
      `IntellectualPropertyPolicy`.
- [x] Form: label anggota "Pemilik KI"; pihak luar boleh berupa institusi
      (mis. "Universitas PMC") — field afiliasi opsional.
- [x] `IntellectualPropertyController`; route `/hki` →
      `intellectual-properties.*`.
- [x] View `intellectual-properties/` (badge jenis KI).
- [x] Navigasi: HKI → `intellectual-properties.index`.
- [x] Detail user: jumlah HKI. `DemoDataSeeder`: 10 HKI.
- [x] `UserPolicy::delete` kini memeriksa kelima modul (pembuat & anggota).
- [x] Test lengkap (`backend-standards.md` §6).
