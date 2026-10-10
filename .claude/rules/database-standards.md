# Database Standards — MySQL 8.0 / Eloquent Migrations

Berlaku untuk `database/migrations`, `database/seeders`,
`database/factories`. Skema lengkap = **PRD §5**
([`PRD-BP2M-PMC.md`](../File%20Skema/BP2M%20v1.0.0/PRD-BP2M-PMC.md)) —
dokumen ini aturan *cara menulisnya*. Setiap perubahan skema: perbarui
PRD §5 di commit yang sama.

## 1. Mesin & tipe

- MySQL 8.0, `utf8mb4_unicode_ci`. Test jalan di SQLite in-memory — jangan
  pakai fungsi khusus MySQL (`FIELD()`, fulltext, `JSON_*`) di query
  aplikasi.
- PK `id()` (BIGINT auto increment) di semua tabel.
- Tahun: `unsignedSmallInteger('year')`, bukan tipe `YEAR` MySQL.
- Kolom jenis/status: **`string` + cast PHP enum**, bukan MySQL `ENUM`.
- Judul: `string('title', 500)` — judul ilmiah sering >255 karakter.

## 2. Penamaan

- Tabel `snake_case` jamak Inggris: `faculties`, `study_programs`,
  `schemes`, `funding_sources`, `users`, `researches`,
  `community_services`, `publications`, `books`,
  `intellectual_properties`, `contributors`, `documents`.
- Model `Research` **wajib** `protected $table = 'researches';`.
- FK `{singular}_id` + `->constrained()`:
  - ke master → `->restrictOnDelete()` (master terpakai tidak bisa
    dihapus; nonaktifkan lewat `is_active`);
  - `created_by` / `updated_by` / `uploaded_by` →
    `->constrained('users')->restrictOnDelete()`;
  - `contributors.user_id` → `nullable()->constrained('users')->restrictOnDelete()`
    (NULL = pihak luar).
- Polimorfik: `$table->morphs('contributable')` / `morphs('documentable')`;
  isi `*_type` dari morph map, bukan nama class. Karena polimorfik tidak
  punya FK, baris `contributors`/`documents` dibersihkan oleh Service saat
  `forceDelete` induknya.
- File migrasi via `php artisan make:migration create_researches_table` —
  jangan tulis nama file manual.

## 3. Kolom wajib tabel data kinerja

Lima tabel (`researches`, `community_services`, `publications`, `books`,
`intellectual_properties`):

- `timestamps()` + `softDeletes()`;
- `created_by` (NOT NULL) + `updated_by` (NULL) → `users`;
- kolom verifikasi: `string('status', 20)->default('DRAFT')->index()`,
  `timestamp('submitted_at')->nullable()`, `timestamp('verified_at')->nullable()`,
  `foreignId('verified_by')->nullable()->constrained('users')->restrictOnDelete()`,
  `text('revision_note')->nullable()`;
- index `year` dan FK master/atribut yang difilter (PRD §5.3).

`verification_logs` **append-only**: hanya `created_at`
(`$table->timestamp('created_at')->useCurrent()`, model `const UPDATED_AT = null`),
model melempar exception di event `updating`/`deleting`, tidak ada route
ubah/hapus. Baris log ikut hilang hanya bila induknya `forceDelete` oleh
Admin. `settings` = key-value sederhana (`key` UNIQUE, `value` TEXT).

## 4. Users

- Role di kolom `users.role` (`ADMIN`/`DOSEN`, cast `UserRole`) — **tanpa**
  Spatie Permission.
- **Tidak ada kolom `email`**; identitas login = `username` (UNIQUE).
- `nidn_nuptk` NULL UNIQUE (wajib untuk Dosen — di Form Request),
  `study_program_id` NULL (Admin boleh tanpa prodi), `is_active`,
  `must_change_password`, `last_login_at`, `remember_token`.
- Hapus migrasi bawaan yang tidak dipakai: kolom `email`/`email_verified_at`
  di `users`, tabel `password_reset_tokens`.

## 5. Index

```php
$table->index('year');                                   // 5 tabel data
$table->index('status');                                 // 5 tabel data → antrean Verifikasi
$table->index(['type', 'is_active']);                    // schemes
$table->index('user_id');                                // contributors → visibleTo
$table->unique(['contributable_type', 'contributable_id', 'sort_order']); // contributors
```

## 6. Seeder & factory

- `DatabaseSeeder` → `MasterDataSeeder` (contoh fakultas/prodi/skema/sumber
  dana — daftar resmi PMC menyusul, PRD §11.1 no. 5; nanti diisi Admin
  lewat UI, seeder tidak perlu diubah), `UserSeeder` (`admin`, `dosen`,
  + ±10 dosen factory), `DemoDataSeeder` (factory ±20 data per modul
  dengan anggota & tanpa file, status campuran: DRAFT, SUBMITTED,
  NEEDS_REVISION, VERIFIED — state factory `draft()`, `submitted()`,
  `needsRevision()`, `verified()`). Menolak jalan bila `app()->isProduction()`.
- Logo tidak di-seed — diunggah Admin lewat Pengaturan.
- `ProductionSeeder` → master + 1 admin (password `ADMIN_INITIAL_PASSWORD`,
  `must_change_password = true`).
- Data dummy lewat Factory (`Research::factory()->withContributors()->count(50)`),
  bukan insert manual.

## 7. Sebelum migrasi

- Migrasi yang `dropColumn`/rename wajib punya `down()` yang benar-benar
  reversibel.
- `php artisan migrate` lokal → database `bp2m_pmc` (root, tanpa password).
