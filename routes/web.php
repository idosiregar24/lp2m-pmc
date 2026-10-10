<?php

use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Auth\ForcePasswordChangeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CommunityServiceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\IntellectualPropertyController;
use App\Http\Controllers\Master\FacultyController;
use App\Http\Controllers\Master\FundingSourceController;
use App\Http\Controllers\Master\SchemeController;
use App\Http\Controllers\Master\StudyProgramController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\PublicLecturerController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

/*
| URL segments are Indonesian (user-facing); route names are English
| (backend-standards.md §3). Ownership is checked by Policies, not here.
*/

// Public portal — no login; verified output of active lecturers only.
Route::middleware('throttle:60,1')->name('public.lecturers.')->group(function () {
    Route::get('/', [PublicLecturerController::class, 'index'])->name('index');
    Route::get('dosen/{lecturer}', [PublicLecturerController::class, 'show'])->whereNumber('lecturer')->name('show');
    Route::get('publik', fn () => redirect()->route('public.lecturers.index'));
    Route::get('publik/dosen/{lecturer}', [PublicLecturerController::class, 'show'])->whereNumber('lecturer');
});

Route::middleware('guest')->group(function () {
    Route::get('masuk', [LoginController::class, 'create'])->name('login');
    Route::post('masuk', [LoginController::class, 'store'])->middleware('throttle:login');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::post('keluar', [LoginController::class, 'destroy'])->name('logout');
    Route::get('ganti-password', [ForcePasswordChangeController::class, 'edit'])->name('password.force');
    Route::put('ganti-password', [ForcePasswordChangeController::class, 'update']);

    Route::middleware(['password.changed', 'throttle:60,1'])->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
        Route::get('profil', [ProfileController::class, 'show'])->name('profile.show');
        Route::put('profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::get('penelitian/ekspor', [ResearchController::class, 'export'])->name('researches.export');
        Route::resource('penelitian', ResearchController::class)
            ->names('researches')->parameters(['penelitian' => 'research']);
        Route::get('pengabdian/ekspor', [CommunityServiceController::class, 'export'])->name('community-services.export');
        Route::resource('pengabdian', CommunityServiceController::class)
            ->names('community-services')->parameters(['pengabdian' => 'communityService']);
        Route::get('publikasi/ekspor', [PublicationController::class, 'export'])->name('publications.export');
        Route::resource('publikasi', PublicationController::class)
            ->names('publications')->parameters(['publikasi' => 'publication']);
        Route::get('buku/ekspor', [BookController::class, 'export'])->name('books.export');
        Route::resource('buku', BookController::class)
            ->names('books')->parameters(['buku' => 'book']);
        Route::get('hki/ekspor', [IntellectualPropertyController::class, 'export'])->name('intellectual-properties.export');
        Route::resource('hki', IntellectualPropertyController::class)
            ->names('intellectual-properties')->parameters(['hki' => 'intellectualProperty']);

        Route::get('dokumen/{document}', [DocumentController::class, 'download'])->name('documents.download');
        Route::delete('dokumen/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

        // {type} = morph alias, whitelisted by App\Support\Verifiable
        Route::post('ajukan/{type}/{id}', SubmissionController::class)
            ->whereNumber('id')->name('submissions.store');

        Route::middleware('role:ADMIN')->group(function () {
            Route::get('verifikasi', [VerificationController::class, 'index'])->name('verifications.index');
            Route::prefix('verifikasi/{type}/{id}')->whereNumber('id')->name('verifications.')->group(function () {
                Route::post('setujui', [VerificationController::class, 'approve'])->name('approve');
                Route::post('kembalikan', [VerificationController::class, 'requestRevision'])->name('return');
                Route::post('buka-kembali', [VerificationController::class, 'reopen'])->name('reopen');
            });

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

            Route::get('rekap', [ReportController::class, 'index'])->name('reports.index');
            Route::get('rekap/ekspor', [ReportController::class, 'export'])->name('reports.export');

            Route::get('pengaturan', [SettingController::class, 'edit'])->name('settings.edit');
            Route::put('pengaturan', [SettingController::class, 'update'])->name('settings.update');
        });
    });
});
