<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo accounts: admin / password, dosen / password, plus 10 lecturers.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $programs = StudyProgram::query()->pluck('id');

        User::factory()->superAdmin()->create([
            'name' => 'Super Admin BP2M',
            'username' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Dr. Sari Wulandari, M.Kep.',
            'username' => 'dosen',
            'nidn_nuptk' => '1012038501',
            'study_program_id' => $programs->first(),
            'role' => UserRole::Dosen,
        ]);

        $excelFile = collect([
            database_path('seeders/data/dosen.xlsx'),
            database_path('seeders/data/dosen.csv'),
            storage_path('app/dosen.xlsx'),
            storage_path('app/dosen.csv'),
        ])->first(fn ($path) => file_exists($path));

        if ($excelFile) {
            app(\App\Services\LecturerImportService::class)->import($excelFile);
        } else {
            User::factory()->count(10)->sequence(fn () => ['study_program_id' => $programs->random()])->create();
        }
    }
}
