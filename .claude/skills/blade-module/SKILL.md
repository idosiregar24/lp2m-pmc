---
name: blade-module
description: Use when scaffolding a BP2M PMC module end-to-end (migration → model → form request → service → policy → controller → route → Blade views → navigation → tests) — e.g. Penelitian, Pengabdian, Publikasi, Buku, HKI, Pengguna, or a Data Master table from PRD §4. Keeps every module built the same way instead of each drifting into its own pattern.
---

# Resep Modul Laravel + Blade

Resep berulang untuk mengubah satu modul PRD §4 / satu sprint di
`.claude/plan/` menjadi kode jalan. Ikuti urutannya — tiap langkah
bergantung pada langkah sebelumnya. Alasan (*kenapa*) ada di
`.claude/rules/*`; file ini *urutannya*.

**Contoh acuan:** modul **Penelitian** (Sprint 3) dibangun pertama dan
memuat semua bagian bersama (anggota, dokumen, Policy, filter). Modul data
kinerja berikutnya **menyalin bentuk Penelitian**, bukan mulai dari file
kosong. Bangun hanya yang di-scope sprint-nya — jangan mendahului.

## 0. Baca dulu

- PRD §4.x modul itu (field + aturan), §5 (tabel), §6 (anggota/dokumen),
  §7 (siapa boleh apa).
- Butir **[ASUMSI]** di PRD: implementasikan sesuai asumsi, tetapi taruh
  di satu tempat yang mudah diubah (Policy / config), jangan tersebar.

## 1. Migrasi

```bash
php artisan make:migration create_researches_table
```

- Nama tabel/kolom persis PRD §5. Kolom wajib data kinerja (title, year,
  created_by, updated_by, timestamps, softDeletes) — `database-standards.md` §3.
- FK master `restrictOnDelete()`; index untuk filter (`year`, FK master).
- `php artisan migrate`.

## 2. Enum (bila modul punya pilihan tetap)

`app/Enums/{Nama}.php` — backed string UPPER_SNAKE + `label()` +
`options()` (+ `tone()` bila tampil sebagai badge).

## 3. Model + factory

- `$fillable` eksplisit (tanpa `created_by`/`updated_by`), `casts()`.
- Data kinerja: `use HasContributors, HasDocuments, HasVerification, SoftDeletes;`,
  relasi `creator()`/`updater()`, relasi master, `scopeVisibleTo()`,
  `scopeFilter()` (termasuk `status`). Kolom verifikasi **tidak** fillable.
- Migrasi data kinerja wajib memuat kolom verifikasi
  (`database-standards.md` §3); factory punya state `draft()`,
  `submitted()`, `needsRevision()`, `verified()`.
- Tambahkan alias modul ke `App\Support\Verifiable` agar muncul di antrean
  Verifikasi dan route `submissions.store` / `verifications.*`.
- `Research`: `protected $table = 'researches';`.
- Daftarkan alias morph di `AppServiceProvider::boot()` (`enforceMorphMap`).
- Master: `scopeActive()` + `static options()`.
- Factory + state `withContributors()` / `withDocuments()`.

## 4. Form Request

`app/Http/Requests/{Modul}/Store{Model}Request.php` (+ `Update…`).

- `authorize()`: `$this->user()->can('create', Research::class)` /
  `can('update', $this->route('research'))`.
- `rules()`: field modul + `use ValidatesContributors, ValidatesDocuments;`
  (gabungkan `$this->contributorRules()` / `$this->documentRules()`).
- `attributes()`: nama field Bahasa Indonesia ("judul penelitian", "tahun").
- Tahun: `['required', 'integer', new ValidYear]`.

## 5. Service

`app/Services/{Model}Service.php` dengan
`create(array $data, array $files, User $actor, bool $submit = false)` dan
`update(Model $m, array $data, array $files, User $actor, bool $submit = false)`:

```php
return DB::transaction(function () use ($data, $files, $actor, $submit) {
    $research = new Research(Arr::except($data, ['contributors']));
    $research->created_by = $actor->id;
    $research->save();

    $this->contributors->sync($research, $data['contributors'], $actor);
    $this->documents->storeMany($research, $files, $actor);

    match (true) {
        $actor->isAdmin() => $this->verification->markVerifiedByAdmin($research, $actor),
        $submit => $this->verification->submit($research, $actor),
        default => null,
    };

    return $research;
});
```

Status **hanya** dipindah oleh `VerificationService` (lihat
`backend-standards.md` §1 "Verifikasi").

Master murni tidak perlu Service.

## 6. Policy

`app/Policies/{Model}Policy.php extends ActivityPolicy` (data kinerja) —
biasanya cukup kosong karena aturan PRD §7 sudah di basis. Laravel
menemukan Policy otomatis dari nama model; daftarkan manual hanya bila
namanya tidak cocok.

## 7. Controller

Tipis. Pola `index`:

```php
public function index(Request $request)
{
    $this->authorize('viewAny', Research::class);

    return view('researches.index', [
        'researches' => Research::query()
            ->visibleTo($request->user())
            ->filter($request->only(['q', 'year', 'faculty', 'study_program', 'scheme', 'funding_source']))
            ->with(['leadContributor.user.studyProgram', 'scheme', 'fundingSource'])
            ->withCount('documents')
            ->latest('year')->latest('id')
            ->paginate(config('bp2m.per_page'))
            ->withQueryString(),
        'schemes' => Scheme::options(SchemeType::Research),
        // … opsi filter lain
    ]);
}
```

`store`/`update` → Service → `redirect()->route('….show', $m)->with('success', '…')`.
`destroy` → authorize → `$m->delete()` → redirect index + flash.

## 8. Route

`routes/web.php` di dalam grup `auth`+`active`+`password.changed`; URL
Indonesia, nama Inggris, `->parameters()` — tabel di
`backend-standards.md` §3. Modul admin di dalam grup `role:ADMIN`.

## 9. View

`resources/views/{route-name}/`:

| File | Isi |
|---|---|
| `index.blade.php` | `page-header` (+ tombol Tambah `@can`) → `filter-bar` (`_filters`, termasuk Status) → `ui.table` (kolom Status = badge) / kartu HP → `links()` |
| `create.blade.php` / `edit.blade.php` | `ui.card` berisi form → `@include('…._form')` |
| `_form.blade.php` | field modul, `contributors-field` (label sesuai modul), `documents-field` (+ daftar dokumen lama dengan tombol hapus di Edit); footer Dosen: Batal · Simpan sebagai Draf · Simpan & Ajukan; Admin: Batal · Simpan |
| `show.blade.php` | `detail-list`, `contributors-list`, `verification-panel`, `documents-list`; tombol Ubah/Hapus `@can` |

Komponen yang belum ada → buat dengan skill `blade-component`. Visual →
skill `front-end-design`. Label wajib = `required` di Form Request.

## 10. Navigasi

Tambah item ke `App\Support\Navigation` (label, route, ikon heroicon,
`routeIs` pola `researches.*`, role) **setelah** route `index` ada.

## 11. Test

`tests/Feature/{Modul}/{Model}Test.php` — daftar minimum di
`backend-standards.md` §6 (tamu, admin CRUD, validasi, dosen milik
sendiri, dosen 403 ke data orang lain, daftar tersaring, unggah/unduh,
alur verifikasi).
Jalankan `php artisan test --filter={Model}` lalu seluruh suite,
`npm run build`, `vendor/bin/pint`.

## 12. Perbarui plan

Centang butir di `.claude/plan/sprint-0N-*.md` (catatan singkat bila
baru sebagian) dan baris status di `plan/README.md`.
