# Sprint 05 — Publikasi & Buku

PRD §4.6, §4.7. Dua modul terpisah (menu Publikasi dan Buku), sama-sama
menyalin bentuk Penelitian.

## Publikasi

- [ ] Enum `VenueType` (Journal → "Jurnal", Proceeding → "Prosiding"),
      `PublicationLevel` (National → "Nasional", International →
      "Internasional", `tone()`).
- [ ] Migrasi `publications` (PRD §5.3, kolom verifikasi; model `HasVerification`).
- [ ] Model `Publication` + factory; `scopeFilter()` (q juga mencari
      `venue_name`/`publisher`, year, faculty, study_program, venue_type,
      level).
- [ ] Form Request + `PublicationService` + `PublicationPolicy`.
- [ ] Form: label "Nama jurnal" / "Nama prosiding" berganti mengikuti
      `venue_type` (Alpine); label anggota "Penulis", baris 1 "Penulis
      pertama".
- [ ] `PublicationController`; route `/publikasi` → `publications.*`.
- [ ] View `publications/` (badge level & jenis wadah di tabel).
- [ ] Navigasi: Publikasi → `publications.index`.

## Buku

- [ ] Migrasi `books` (PRD §5.3, kolom verifikasi; model `HasVerification`).
- [ ] Model `Book` + factory; `scopeFilter()` (q juga mencari `publisher`,
      year, faculty, study_program).
- [ ] Form Request + `BookService` + `BookPolicy`.
- [ ] `BookController`; route `/buku` → `books.*`.
- [ ] View `books/` (label anggota "Penulis").
- [ ] Navigasi: Buku → `books.index`.

## Bersama

- [ ] Alias `publication` & `book` di `Verifiable`; form & detail memakai tombol/panel verifikasi.
- [ ] Detail user: jumlah publikasi & buku. `DemoDataSeeder`: 20 + 10.
- [ ] Test lengkap kedua modul (`backend-standards.md` §6) + enum tidak
      valid ditolak.
