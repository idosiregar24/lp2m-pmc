# Sprint 05 — Publikasi & Buku

PRD §4.6, §4.7. Dua modul terpisah (menu Publikasi dan Buku), sama-sama
menyalin bentuk Penelitian.

## Publikasi

- [x] Enum `VenueType` (Journal → "Jurnal", Proceeding → "Prosiding"),
      `PublicationLevel` (National → "Nasional", International →
      "Internasional", `tone()`).
- [x] Migrasi `publications` (PRD §5.3, kolom verifikasi; model `HasVerification`).
- [x] Model `Publication` + factory; `scopeFilter()` (q juga mencari
      `venue_name`/`publisher`, year, faculty, study_program, venue_type,
      level).
- [x] Form Request + `PublicationService` + `PublicationPolicy`.
- [x] Form: label "Nama jurnal" / "Nama prosiding" berganti mengikuti
      `venue_type` (Alpine); label anggota "Penulis", baris 1 "Penulis
      pertama".
- [x] `PublicationController`; route `/publikasi` → `publications.*`.
- [x] View `publications/` (badge level & jenis wadah di tabel).
- [x] Navigasi: Publikasi → `publications.index`.

## Buku

- [x] Migrasi `books` (PRD §5.3, kolom verifikasi; model `HasVerification`).
- [x] Model `Book` + factory; `scopeFilter()` (q juga mencari `publisher`,
      year, faculty, study_program).
- [x] Form Request + `BookService` + `BookPolicy`.
- [x] `BookController`; route `/buku` → `books.*`.
- [x] View `books/` (label anggota "Penulis").
- [x] Navigasi: Buku → `books.index`.

## Bersama

- [x] Alias `publication` & `book` di `Verifiable`; form & detail memakai tombol/panel verifikasi.
- [x] Detail user: jumlah publikasi & buku. `DemoDataSeeder`: 20 + 10.
- [x] Test lengkap kedua modul (`backend-standards.md` §6) + enum tidak
      valid ditolak.
