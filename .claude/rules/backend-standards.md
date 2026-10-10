# Backend Standards — Laravel 12 / PHP 8.4

Berlaku untuk semua kode di `app/`, `routes/`, `config/`. Lihat juga
[`database-standards.md`](database-standards.md) untuk migrasi/skema dan
[`security-standards.md`](security-standards.md) untuk auth/validasi/file.
Urutan membangun modul: skill `blade-module`.

## 1. Arsitektur: thin controller, logic di Service

```
Controller   → orkestrasi saja: authorize, Form Request, panggil Service,
               return view() / redirect()->with('success', …).
Form Request → SEMUA validasi input + nama atribut Bahasa Indonesia.
Policy       → siapa boleh view/update/delete satu record (PRD §7).
Service      → simpan yang menyentuh >1 tabel (data + contributors +
               documents) di dalam DB::transaction().
Model        → relasi, cast, scope (visibleTo, filter), accessor ringan.
```

```php
// app/Http/Controllers/ResearchController.php
public function store(StoreResearchRequest $request, ResearchService $service)
{
    $research = $service->create(
        $request->validated(),
        $request->file('documents', []),
        $request->user(),
        submit: $request->boolean('submit'),   // tombol "Simpan & Ajukan"
    );

    return redirect()->route('researches.show', $research)
        ->with('success', 'Data penelitian berhasil disimpan.');
}
```

`ResearchService::create()`:

```php
return DB::transaction(function () use ($data, $files, $actor, $submit) {
    $research = new Research(Arr::except($data, ['contributors']));
    $research->created_by = $actor->id;
    $research->save();                                   // status default DRAFT

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

`DocumentService` mencatat path yang sudah ditulis dan menghapusnya bila
transaksi gagal (file tidak ikut rollback). Master murni (fakultas, prodi,
skema, sumber dana) tidak perlu Service.

### Verifikasi (PRD §4.11, §6.4)

- `status`, `submitted_at`, `verified_at`, `verified_by`, `revision_note`
  **tidak** fillable dan tidak pernah divalidasi dari form.
- `VerificationService` satu-satunya yang memindah status:
  `submit()` (DRAFT/NEEDS_REVISION → SUBMITTED, cek ≥1 dokumen bila
  `config('bp2m.verification.require_document')`), `verify()`
  (SUBMITTED → VERIFIED), `requestRevision($note)` (SUBMITTED →
  NEEDS_REVISION), `reopen($note)` (VERIFIED → NEEDS_REVISION),
  `markVerifiedByAdmin()` (status apa pun → VERIFIED, saat Admin
  membuat/mengubah). Setiap method: kunci baris (`lockForUpdate`), cek
  transisi lewat `VerificationStatus::canTransitionTo()`, tulis
  `VerificationLog`, semua dalam satu transaksi. Transisi ilegal →
  `InvalidStatusTransition` (pesan Bahasa Indonesia, ditampilkan sebagai
  flash error).
- Update oleh Dosen pada data `NEEDS_REVISION` tidak mengubah status
  (kecuali ia menekan "Simpan & Ajukan").

## 2. Lokasi file

```
app/Enums/                    UserRole, SchemeType, StudyProgramLevel, VenueType,
                              PublicationLevel, IntellectualPropertyType, VerificationStatus
app/Exceptions/               InvalidStatusTransition
app/Http/Controllers/         DashboardController, ProfileController, DocumentController,
                              SubmissionController, ResearchController,
                              CommunityServiceController, PublicationController,
                              BookController, IntellectualPropertyController
app/Http/Controllers/Auth/    LoginController, ForcePasswordChangeController
app/Http/Controllers/Admin/   UserController, VerificationController, SettingController,
                              ReportController
app/Http/Controllers/Master/  FacultyController, StudyProgramController,
                              SchemeController, FundingSourceController
app/Http/Requests/{Auth,Profile,Admin,Master,Research,CommunityService,Publication,Book,IntellectualProperty}/
app/Http/Requests/Concerns/   ValidatesContributors, ValidatesDocuments
app/Http/Middleware/          EnsureRole (alias role), EnsureUserIsActive (active),
                              EnsurePasswordChanged (password.changed)
app/Models/                   flat: User, Faculty, StudyProgram, Scheme, FundingSource,
                              Research, CommunityService, Publication, Book,
                              IntellectualProperty, Contributor, Document,
                              VerificationLog, Setting
app/Models/Concerns/          HasContributors, HasDocuments, HasVerification
app/Policies/                 ActivityPolicy (abstract) + {Model}Policy, UserPolicy, DocumentPolicy
app/Rules/                    ValidYear
app/Services/                 {Model}Service, ContributorService, DocumentService,
                              VerificationService, UserService, SettingService
app/Support/Navigation.php    sumber menu sidebar
app/Support/Settings.php      baca setting (cache), logoUrl()
app/Support/Verifiable.php    peta alias morph → model untuk route verifikasi (404 bila asing)
app/Exports/                  Sprint 7 (Laravel Excel)
config/bp2m.php               per_page, batas dokumen, tahun minimum,
                              verification.require_document, logo (mimes, max_kb)
```

## 3. Routing (`routes/web.php`)

URL Bahasa Indonesia, **nama route Bahasa Inggris** (dipakai `route()` dan
nama folder view), parameter = nama model:

```php
Route::middleware('guest')->group(function () {
    Route::get('masuk', [LoginController::class, 'create'])->name('login');
    Route::post('masuk', [LoginController::class, 'store'])->middleware('throttle:login');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('keluar', [LoginController::class, 'destroy'])->name('logout');
    Route::get('ganti-password', [ForcePasswordChangeController::class, 'edit'])->name('password.force');
    Route::put('ganti-password', [ForcePasswordChangeController::class, 'update']);

    Route::middleware(['password.changed', 'throttle:60,1'])->group(function () {
        Route::get('/', DashboardController::class)->name('dashboard');
        Route::get('profil', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::resource('penelitian', ResearchController::class)
            ->names('researches')->parameters(['penelitian' => 'research']);
        Route::resource('pengabdian', CommunityServiceController::class)
            ->names('community-services')->parameters(['pengabdian' => 'communityService']);
        Route::resource('publikasi', PublicationController::class)
            ->names('publications')->parameters(['publikasi' => 'publication']);
        Route::resource('buku', BookController::class)
            ->names('books')->parameters(['buku' => 'book']);
        Route::resource('hki', IntellectualPropertyController::class)
            ->names('intellectual-properties')->parameters(['hki' => 'intellectualProperty']);

        Route::get('dokumen/{document}', [DocumentController::class, 'download'])->name('documents.download');
        Route::delete('dokumen/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
        // Sprint 7: Route::get('{module}/ekspor', …)->name('{module}.export')

        // {type} = alias morph (research, community_service, …) — di-whitelist lewat App\Support\Verifiable
        Route::post('ajukan/{type}/{id}', SubmissionController::class)->name('submissions.store');

        Route::middleware('role:ADMIN')->group(function () {
            Route::get('verifikasi', [VerificationController::class, 'index'])->name('verifications.index');
            Route::post('verifikasi/{type}/{id}/setujui', [VerificationController::class, 'approve'])->name('verifications.approve');
            Route::post('verifikasi/{type}/{id}/kembalikan', [VerificationController::class, 'return'])->name('verifications.return');
            Route::post('verifikasi/{type}/{id}/buka-kembali', [VerificationController::class, 'reopen'])->name('verifications.reopen');

            Route::get('pengaturan', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');

            Route::resource('pengguna', UserController::class)
                ->names('users')->parameters(['pengguna' => 'user']);
            Route::patch('pengguna/{user}/status', [UserController::class, 'toggleActive'])->name('users.toggle-active');
            Route::patch('pengguna/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

            Route::prefix('master')->name('master.')->group(function () {
                Route::resource('fakultas', FacultyController::class)->except('show')
                    ->names('faculties')->parameters(['fakultas' => 'faculty']);
                Route::resource('prodi', StudyProgramController::class)->except('show')
                    ->names('study-programs')->parameters(['prodi' => 'studyProgram']);
                Route::resource('skema', SchemeController::class)->except('show')
                    ->names('schemes')->parameters(['skema' => 'scheme']);
                Route::resource('sumber-dana', FundingSourceController::class)->except('show')
                    ->names('funding-sources')->parameters(['sumber-dana' => 'fundingSource']);
            });

            Route::get('rekap', ReportController::class)->name('reports.index'); // Sprint 7
        });
    });
});
```

- Jangan hardcode path di Blade — selalu `route('researches.index')`.
- Begitu `index` suatu modul ada, isi `route` item-nya di
  `App\Support\Navigation`.

## 4. Eloquent

- `$fillable` eksplisit (bukan `$guarded = []`); `created_by`/`updated_by`
  **tidak** fillable — diisi Service dari `$actor->id`.
- Cast via method `casts()`: enum, `year` → `integer`, `is_active` →
  `boolean`, `password` → `hashed`.
- `HasContributors` (5 model data): `contributors()` (morphMany urut
  `sort_order`), `leadContributor()` (morphOne `sort_order = 1`),
  `scopeVisibleTo(User)`, `isLedOrCreatedBy(User)`,
  `scopeFilterByUnit($faculty, $studyProgram)` (lewat anggota internal).
- `HasDocuments`: `documents()` morphMany.
- `HasVerification`: cast `status` → `VerificationStatus`, `verifier()`,
  `verificationLogs()` (morphMany, terbaru dulu), `scopeVerified()`,
  `scopeStatus(?VerificationStatus)`, `isLockedForOwner()` (SUBMITTED /
  VERIFIED).
- `scopeFilter(array $filters)` per model: `q` (judul atau nama anggota),
  `year`, `faculty`, `study_program`, `status` + atribut modul (`scheme`,
  `funding_source`, `level`, `venue_type`, `type`). Kunci filter = nama
  query string.
- Index wajib eager-load: `->with(['leadContributor.user.studyProgram', …master])->withCount('documents')`.
- Paginasi: `->latest('year')->latest('id')->paginate(config('bp2m.per_page'))->withQueryString()`.
- Master: `scopeActive()` + `public static function options(): array`
  (id → nama, aktif saja, urut nama). `Scheme::options(SchemeType $type)`.

## 5. Enum

Backed string enum di `app/Enums/`: case PascalCase, nilai UPPER_SNAKE
Inggris, `label()` Bahasa Indonesia, `options()` untuk select, `tone()` bila
tampil sebagai badge.

```php
enum PublicationLevel: string
{
    case National = 'NATIONAL';
    case International = 'INTERNATIONAL';

    public function label(): string
    {
        return match ($this) {
            self::National => 'Nasional',
            self::International => 'Internasional',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::National => 'info',
            self::International => 'accent',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }
}
```

Validasi: `Rule::enum(PublicationLevel::class)`.

## 6. Testing

- Pest, `tests/Feature/{Modul}/{Model}Test.php`, target coverage ≥70%.
- Daftar minimum per modul data:
  1. tamu → redirect `login`;
  2. Admin: index/create/store/show/edit/update/destroy;
  3. validasi wajib + `ValidYear` + skema dari jenis lain ditolak;
  4. Dosen membuat data → dirinya otomatis anggota urutan 1;
  5. Dosen melihat/ubah data miliknya (pembuat / ketua) → sukses;
  6. Dosen ke data dosen lain (show/edit/update/destroy) → 403;
  7. index Dosen tidak memuat data orang lain (`assertDontSee`);
  8. unggah PDF tersimpan di disk `local`, non-PDF ditolak, unduh oleh
     yang berhak 200 / tidak berhak 403 (`Storage::fake('local')`);
  9. verifikasi: data Dosen lahir DRAFT, "Simpan & Ajukan" → SUBMITTED,
     ajukan tanpa dokumen ditolak, Dosen tidak bisa ubah/hapus data
     SUBMITTED/VERIFIED (403), data Admin langsung VERIFIED, Admin
     setujui/kembalikan (catatan wajib)/buka kembali, transisi ilegal
     ditolak, setiap transisi menulis `verification_logs`, `status`
     yang diselipkan di request diabaikan.
- Modul admin (Pengguna, Master, Verifikasi, Pengaturan): Admin 200,
  Dosen 403, tamu → login.
- Factory untuk setiap model, state `withContributors()` / `withDocuments()`.

## 7. Gaya kode

- PSR-12 lewat `vendor/bin/pint` sebelum commit.
- Nama deskriptif, jangan singkat (`communityService`, bukan `cs`/`pkm`).
- Pesan ke user (flash, validasi, exception domain) Bahasa Indonesia.
