# Sprint 03 — Fondasi Bersama + Penelitian (modul acuan)

PRD §4.4, §4.9, §5.3–5.4, §6, §7. Ikuti skill `blade-module` langkah demi
langkah — modul ini menjadi cetakan untuk Sprint 04–06, jadi kerjakan rapi.

## Fondasi bersama (dipakai 5 modul)

- [x] Migrasi `contributors` & `documents` (PRD §5.4).
- [x] Morph map `enforceMorphMap` di `AppServiceProvider` (5 alias).
- [x] Model `Contributor` (`user()`, `displayName()` = user->name atau
      name, `isExternal()`), `Document` (`uploader()`, `humanSize()`).
- [x] Trait `HasContributors`: `contributors()`, `leadContributor()`,
      `scopeVisibleTo(User)`, `isLedOrCreatedBy(User)`,
      `scopeFilterByUnit($faculty, $studyProgram)`, pencarian nama anggota.
- [x] Trait `HasDocuments`: `documents()`.
- [x] `ContributorService::sync($model, $rows, $actor)`: hapus-tulis ulang
      dalam transaksi, `sort_order` 1..n, Dosen pembuat otomatis ada,
      tolak hapus diri sendiri & duplikat.
- [x] `DocumentService::storeMany()` / `delete()` (disk `local`, nama ULID,
      bersihkan file bila transaksi gagal).
- [x] Request concern `ValidatesContributors`, `ValidatesDocuments`;
      `App\Rules\ValidYear` (+ `ValidYear::options()` untuk select tahun).
- [x] `ActivityPolicy` (abstract) + `DocumentPolicy`;
      `DocumentController` download/destroy.
- [x] Komponen `shared/`: filter-bar, contributors-field (Alpine + Tom
      Select, toggle Dosen PMC / Luar PMC, urutan naik/turun, label "Ketua"
      di baris 1), contributors-list, documents-field, documents-list,
      detail-list, delete-button; `ui/`: table, stat-card, tabs (bila
      belum).
- [x] Paginasi kustom `vendor/pagination/tailwind.blade.php` (token PMC).

## Fondasi verifikasi (PRD §4.11, §6.4)

- [x] Enum `VerificationStatus` (Draft, Submitted, NeedsRevision,
      Verified; `label()`, `tone()`, `canTransitionTo()`).
- [x] Migrasi `verification_logs` (append-only) + model `VerificationLog`
      (tanpa `updated_at`, tolak update/delete).
- [x] Trait `HasVerification` (cast, `verifier()`, `verificationLogs()`,
      `scopeVerified()`, `scopeStatus()`, `isLockedForOwner()`).
- [x] `VerificationService`: `submit`, `verify`, `requestRevision`,
      `reopen`, `markVerifiedByAdmin` — `lockForUpdate`, cek transisi, tulis
      log, satu transaksi; `InvalidStatusTransition` → flash error.
- [x] `config('bp2m.verification.require_document')` = `true`.
- [x] `App\Support\Verifiable` (alias morph → model, 404 bila asing).
- [x] `ActivityPolicy`: `update`/`delete`/`submit` mensyaratkan status
      DRAFT/NEEDS_REVISION untuk Dosen; `verify`/`requestRevision`/`reopen`
      Admin saja.
- [x] `SubmissionController` (Dosen "Ajukan" dari halaman detail).
- [x] `Admin\VerificationController`: `index` (antrean SUBMITTED lintas
      modul, tab per modul + Semua, urut `submitted_at` terlama, filter
      fakultas/prodi/tahun), `approve`, `return` (catatan wajib ≤1000),
      `reopen` (catatan wajib).
- [x] Komponen `shared/verification-panel` (status, catatan revisi, aksi
      `@can`, modal catatan, riwayat).
- [x] Angka antrean di menu Verifikasi (`Navigation`) + item Navigasi →
      `verifications.index`.

## Penelitian

- [x] Migrasi `researches` (PRD §5.3, termasuk kolom verifikasi) — model
      `Research` `protected $table = 'researches'`, `use HasVerification`.
- [x] Form: tombol Simpan sebagai Draf / Simpan & Ajukan (Dosen), Simpan
      (Admin → langsung Terverifikasi).
- [x] Daftar: kolom & filter Status; detail: `verification-panel`.
- [x] Model + factory (`withContributors()`, `withDocuments()`),
      `scopeFilter()` (q, year, faculty, study_program, scheme,
      funding_source).
- [x] `StoreResearchRequest` / `UpdateResearchRequest` (skema tipe
      Research & aktif; sumber dana aktif).
- [x] `ResearchService` create/update; `ResearchPolicy extends ActivityPolicy`.
- [x] `ResearchController` resource; route `/penelitian` → `researches.*`.
- [x] View `researches/`: index (+ kartu HP), `_filters`, create, edit,
      `_form` (label anggota "Peneliti"), show.
- [x] Navigasi: Penelitian → `researches.index`.
- [x] Detail user (Sprint 02) menampilkan jumlah penelitian.
- [x] `DemoDataSeeder`: 20 penelitian.

## Test (daftar `backend-standards.md` §6, semuanya)

- [x] Tamu, Admin CRUD, validasi, Dosen otomatis anggota, Dosen milik
      sendiri, Dosen 403 ke data lain, index tersaring, unggah/unduh PDF,
      non-PDF ditolak, maks 10 file, hapus dokumen menghapus file.
- [x] `ContributorService`: duplikat ditolak, Dosen tidak bisa menghapus
      dirinya, urutan tersimpan.
- [x] Verifikasi (butir 9 `backend-standards.md` §6): semua transisi sah,
      transisi ilegal ditolak, ajukan tanpa dokumen ditolak, Dosen 403
      ubah/hapus data terkunci, `status` dari request diabaikan, log
      tertulis & tidak bisa diubah, antrean hanya untuk Admin, alias
      `{type}` asing → 404.
