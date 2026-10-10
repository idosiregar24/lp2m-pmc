<?php

namespace Database\Seeders;

use App\Enums\SchemeType;
use App\Enums\StudyProgramLevel;
use App\Models\Faculty;
use App\Models\FundingSource;
use App\Models\Scheme;
use App\Models\StudyProgram;
use Illuminate\Database\Seeder;

/**
 * SAMPLE master data — the official Universitas PMC list has not been received
 * yet (PRD §11.1 no. 5). Admin corrects it from Data Master; no code change needed.
 * Idempotent: safe to run more than once.
 */
class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $faculties = [
            ['Fakultas Ilmu Kesehatan', 'FIKES', [
                [StudyProgramLevel::S1, 'Keperawatan'],
                [StudyProgramLevel::Profesi, 'Ners'],
                [StudyProgramLevel::D3, 'Kebidanan'],
            ]],
            ['Fakultas Farmasi', 'FF', [
                [StudyProgramLevel::S1, 'Farmasi'],
                [StudyProgramLevel::D3, 'Farmasi'],
            ]],
            ['Fakultas Teknologi Kesehatan', 'FTK', [
                [StudyProgramLevel::D3, 'Teknologi Laboratorium Medis'],
                [StudyProgramLevel::S1, 'Administrasi Rumah Sakit'],
            ]],
        ];

        foreach ($faculties as [$name, $code, $programs]) {
            $faculty = Faculty::firstOrCreate(['name' => $name], ['code' => $code, 'is_active' => true]);

            foreach ($programs as [$level, $programName]) {
                StudyProgram::firstOrCreate(
                    ['faculty_id' => $faculty->id, 'name' => $programName, 'level' => $level],
                    ['is_active' => true],
                );
            }
        }

        $schemes = [
            SchemeType::Research->value => ['Penelitian Dosen Pemula', 'Penelitian Dasar', 'Penelitian Terapan', 'Penelitian Internal'],
            SchemeType::CommunityService->value => ['Pemberdayaan Kemitraan Masyarakat', 'Pengabdian Internal', 'Pemberdayaan Berbasis Masyarakat'],
        ];

        foreach ($schemes as $type => $names) {
            foreach ($names as $name) {
                Scheme::firstOrCreate(['type' => $type, 'name' => $name], ['is_active' => true]);
            }
        }

        foreach (['Internal Universitas PMC', 'Hibah Kemdiktisaintek', 'Mandiri', 'Mitra/Industri'] as $name) {
            FundingSource::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
