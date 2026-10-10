# Sprint 03 — Fondasi Bersama + Penelitian (modul acuan)

PRD §4.4, §4.9, §5.3–5.4, §6, §7. Ikuti skill `blade-module` langkah demi
langkah — modul ini menjadi cetakan untuk Sprint 04–06, jadi kerjakan rapi.

## Fondasi bersama (dipakai 5 modul)

- [ ] Migrasi `contributors` & `documents` (PRD §5.4).
- [ ] Morph map `enforceMorphMap` di `AppServiceProvider` (5 alias).
- [ ] Model `Contributor` (`user()`, `displayName()` = user->name atau
      name, `isExternal()`), `Document` (`uploader()`, `humanSize()`).
- [ ] Trait `HasContributors`: `contributors()`, `leadContributor()`,
      `scopeVisibleTo(User)`, `isLedOrCreatedBy(User)`,
      `scopeFilterByUnit($faculty, $studyProgram)`, pencarian nama anggota.
- [ ] Trait `HasDocuments`: `documents()`.
- [ ] `ContributorService::sync($model, $rows, $actor)`: hapus-tulis ulang
      dalam transaksi, `sort_order` 1..n, Dosen pembuat otomatis ada,
      tolak hapus diri sendiri & duplikat.
- [ ] `DocumentService::storeMany()` / `delete()` (disk `local`, nama ULID,
      bersihkan file bila transaksi gagal).
- [ ] Request concern `ValidatesContributors`, `ValidatesDocuments`;
      `App\Rules\ValidYear` (+ `ValidYear::options()` untuk select tahun).
- [ ] `ActivityPolicy` (abstract) + `DocumentPolicy`;
      `DocumentController` download/destroy.
- [ ] Komponen `shared/`: filter-bar, contributors-field (Alpine + Tom
      Select, toggle Dosen PMC / Luar PMC, urutan naik/turun, label "Ketua"
      di baris 1), contributors-list, documents-field, documents-list,
      detail-list, delete-button; `ui/`: table, stat-card, tabs (bila
      belum).
- [ ] Paginasi kustom `vendor/pagination/tailwind.blade.php` (token PMC).

## Fondasi verifikasi (PRD §4.11, §6.4)

- [ ] Enum `VerificationStatus` (Draft, Submitted, NeedsRevision,
      Verified; `label()`, `tone()`, `canTransitionTo()`).
- [ ] Migrasi `verification_logs` (append-only) + model `VerificationLog`
      (tanpa `updated_at`, tolak update/delete).
- [ ] Trait `HasVerification` (cast, `verifier()`, `verificationLogs()`,
      `scopeVerified()`, `scopeStatus()`, `isLockedForOwner()`).
- [ ] `VerificationService`: `submit`, `verify`, `requestRevision`,
      `reopen`, `markVerifiedByAdmin` — `lockForUpdate`, cek transisi, tulis
      log, satu transaksi; `InvalidStatusTransition` → flash error.
- [ ] `config('bp2m.verification.require_document')` = `true`.
- [ ] `App\Support\Verifiable` (alias morph → model, 404 bila asing).
- [ ] `ActivityPolicy`: `update`/`delete`/`submit` mensyaratkan status
      DRAFT/NEEDS_REVISION untuk Dosen; `verify`/`requestRevision`/`reopen`
      Admin saja.
- [ ] `SubmissionController` (Dosen "Ajukan" dari halaman detail).
- [ ] `Admin\VerificationController`: `index` (antrean SUBMITTED lintas
      modul, tab per modul + Semua, urut `submitted_at` terlama, filter
      fakultas/prodi/tahun), `approve`, `return` (catatan wajib ≤1000),
      `reopen` (catatan wajib).
- [ ] Komponen `shared/verification-panel` (status, catatan revisi, aksi
      `@can`, modal catatan, riwayat).
- [ ] Angka antrean di menu Verifikasi (`Navigation`) + item Navigasi →
      `verifications.index`.

## Penelitian

- [ ] Migrasi `researches` (PRD §5.3, termasuk kolom verifikasi) — model
      `Research` `protected $table = 'researches'`, `use HasVerification`.
- [ ] Form: tombol Simpan sebagai Draf / Simpan & Ajukan (Dosen), Simpan
      (Admin → langsung Terverifikasi).
- [ ] Daftar: kolom & filter Status; detail: `verification-panel`.
- [ ] Model + factory (`withContributors()`, `withDocuments()`),
      `scopeFilter()` (q, year, faculty, study_program, scheme,
      funding_source).
- [ ] `StoreResearchRequest` / `UpdateResearchRequest` (skema tipe
      Research & aktif; sumber dana aktif).
- [ ] `ResearchService` create/update; `ResearchPolicy extends ActivityPolicy`.
- [ ] `ResearchController` resource; route `/penelitian` → `researches.*`.
- [ ] View `researches/`: index (+ kartu HP), `_filters`, create, edit,
      `_form` (label anggota "Peneliti"), show.
- [ ] Navigasi: Penelitian → `researches.index`.
- [ ] Detail user (Sprint 02) menampilkan jumlah penelitian.
- [ ] `DemoDataSeeder`: 20 penelitian.

## Test (daftar `backend-standards.md` §6, semuanya)

- [ ] Tamu, Admin CRUD, validasi, Dosen otomatis anggota, Dosen milik
      sendiri, Dosen 403 ke data lain, index tersaring, unggah/unduh PDF,
      non-PDF ditolak, maks 10 file, hapus dokumen menghapus file.
- [ ] `ContributorService`: duplikat ditolak, Dosen tidak bisa menghapus
      dirinya, urutan tersimpan.
- [ ] Verifikasi (butir 9 `backend-standards.md` §6): semua transisi sah,
      transisi ilegal ditolak, ajukan tanpa dokumen ditolak, Dosen 403
      ubah/hapus data terkunci, `status` dari request diabaikan, log
      tertulis & tidak bisa diubah, antrean hanya untuk Admin, alias
      `{type}` asing → 404.
