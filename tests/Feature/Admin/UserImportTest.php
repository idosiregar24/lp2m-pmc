<?php

use App\Enums\UserRole;
use App\Models\Faculty;
use App\Models\StudyProgram;
use App\Models\User;
use App\Services\LecturerImportService;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

it('requires admin authorization for excel import and template', function () {
    $this->get(route('users.import'))->assertRedirect(route('login'));
    $this->get(route('users.import.template'))->assertRedirect(route('login'));

    $this->actingAs(dosen())->get(route('users.import'))->assertForbidden();
    $this->actingAs(dosen())->get(route('users.import.template'))->assertForbidden();

    $this->actingAs(admin())->get(route('users.import'))->assertOk();
    $this->actingAs(admin())->get(route('users.import.template'))->assertOk();
});

it('downloads the excel template', function () {
    $response = $this->actingAs(admin())->get(route('users.import.template'));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=template-data-dosen-pmc.xlsx');
});

it('imports lecturers from excel file successfully', function () {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([
        ['Fakultas', 'Prodi', 'Nama Dosen', 'NIDN', 'NUPTK'],
        ['Kesehatan', 'Gizi', 'Anugerah Humairah, S.Gz, M.kes', '1019099006', '2251768669230350'],
        ['Kesehatan', 'Ilmu Keperawatan', 'Ns. Wahyu Saputra, M.Kep', '', ''],
        ['Ekonomi dan Bisnis', 'Bisnis Digital', 'Fadhly Alfallah, S.Kom., M.Kom', '-', '7138779680130080'],
    ]);

    $tempFile = tempnam(sys_get_temp_dir(), 'test_import_') . '.xlsx';
    (new Xlsx($spreadsheet))->save($tempFile);

    $uploadedFile = new UploadedFile($tempFile, 'dosen.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

    $response = $this->actingAs(admin())->post(route('users.import.process'), [
        'file' => $uploadedFile,
        'update_existing' => 1,
    ]);

    $response->assertRedirect(route('users.index'));
    $response->assertSessionHas('success');

    // Assert users created with single-word usernames
    $user1 = User::query()->where('name', 'Anugerah Humairah, S.Gz, M.kes')->first();
    expect($user1)->not->toBeNull()
        ->and($user1->role)->toBe(UserRole::Dosen)
        ->and($user1->username)->toBe('anugerah')
        ->and($user1->nidn_nuptk)->toBe('1019099006')
        ->and($user1->must_change_password)->toBeTrue()
        ->and($user1->is_active)->toBeTrue();

    // Assert auto-created Faculty and Study Program
    $ekonomiFaculty = Faculty::query()->where('name', 'like', '%Ekonomi dan Bisnis%')->first();
    expect($ekonomiFaculty)->not->toBeNull();

    $bisnisDigitalProdi = StudyProgram::query()->where('name', 'Bisnis Digital')->where('faculty_id', $ekonomiFaculty->id)->first();
    expect($bisnisDigitalProdi)->not->toBeNull();

    $user3 = User::query()->where('name', 'Fadhly Alfallah, S.Kom., M.Kom')->first();
    expect($user3)->not->toBeNull()
        ->and($user3->username)->toBe('fadhly')
        ->and($user3->study_program_id)->toBe($bisnisDigitalProdi->id)
        ->and($user3->nidn_nuptk)->toBe('7138779680130080');

    // Assert user with empty NIDN/NUPTK
    $user2 = User::query()->where('name', 'Ns. Wahyu Saputra, M.Kep')->first();
    expect($user2)->not->toBeNull()
        ->and($user2->username)->toBe('wahyu')
        ->and($user2->nidn_nuptk)->toBeNull();

    @unlink($tempFile);
});

it('updates existing lecturers when re-imported', function () {
    $faculty = Faculty::factory()->create(['name' => 'Fakultas Kesehatan']);
    $prodi = StudyProgram::factory()->create(['faculty_id' => $faculty->id, 'name' => 'Gizi']);
    $existing = dosen([
        'name' => 'Anugerah Humairah, S.Gz',
        'nidn_nuptk' => '1019099006',
        'study_program_id' => $prodi->id,
    ]);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([
        ['Fakultas', 'Prodi', 'Nama Dosen', 'NIDN', 'NUPTK'],
        ['Kesehatan', 'Gizi', 'Anugerah Humairah, S.Gz, M.kes', '1019099006', '2251768669230350'],
    ]);

    $tempFile = tempnam(sys_get_temp_dir(), 'test_update_') . '.xlsx';
    (new Xlsx($spreadsheet))->save($tempFile);

    $uploadedFile = new UploadedFile($tempFile, 'dosen.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

    $this->actingAs(admin())->post(route('users.import.process'), [
        'file' => $uploadedFile,
        'update_existing' => 1,
    ])->assertRedirect(route('users.index'));

    $existing->refresh();
    expect($existing->name)->toBe('Anugerah Humairah, S.Gz, M.kes');

    @unlink($tempFile);
});

it('validates the uploaded excel file format', function () {
    $this->actingAs(admin())->post(route('users.import.process'), [
        'file' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
    ])->assertSessionHasErrors('file');
});

it('runs the import lecturers artisan command', function () {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([
        ['Fakultas', 'Prodi', 'Nama Dosen', 'NIDN', 'NUPTK'],
        ['Kesehatan', 'Kebidanan', 'Bdn. Desy Winda, SST., M.Kes', '1020128902', '2552767668231030'],
    ]);

    $tempFile = tempnam(sys_get_temp_dir(), 'test_cli_') . '.xlsx';
    (new Xlsx($spreadsheet))->save($tempFile);

    $this->artisan('lppm:import-lecturers', ['file' => $tempFile])
        ->assertSuccessful();

    expect(User::query()->where('name', 'Bdn. Desy Winda, SST., M.Kes')->exists())->toBeTrue();

    @unlink($tempFile);
});

it('previews spreadsheet data via preview endpoint', function () {
    $this->post(route('users.import.preview'))->assertRedirect(route('login'));
    $this->actingAs(dosen())->post(route('users.import.preview'))->assertForbidden();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([
        ['Fakultas', 'Prodi', 'Nama Dosen', 'NIDN', 'NUPTK'],
        ['Kesehatan', 'Gizi', 'Dosen Pratama, S.Gz', '1019099001', '2251768669230001'],
        ['Ekonomi', 'Bisnis Digital', 'Dosen Kedua, M.Kom', '1019099002', ''],
    ]);

    $tempFile = tempnam(sys_get_temp_dir(), 'test_preview_') . '.xlsx';
    (new Xlsx($spreadsheet))->save($tempFile);

    $uploadedFile = new UploadedFile($tempFile, 'data_dosen.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);

    $response = $this->actingAs(admin())->postJson(route('users.import.preview'), [
        'file' => $uploadedFile,
    ]);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.file_name', 'data_dosen.xlsx')
        ->assertJsonPath('data.total_rows', 2)
        ->assertJsonPath('data.header_row', 1)
        ->assertJsonPath('data.sample_rows.0.nama', 'Dosen Pratama, S.Gz')
        ->assertJsonPath('data.columns.nama.letter', 'C');

    @unlink($tempFile);
});


