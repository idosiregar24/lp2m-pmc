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

        User::factory()->admin()->create([
            'name' => 'Admin BP2M',
            'username' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Dr. Sari Wulandari, M.Kep.',
            'username' => 'dosen',
            'nidn_nuptk' => '1012038501',
            'study_program_id' => $programs->first(),
            'role' => UserRole::Dosen,
        ]);

        User::factory()->count(10)->sequence(fn () => ['study_program_id' => $programs->random()])->create();
    }
}
