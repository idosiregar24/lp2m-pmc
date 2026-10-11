<?php

use App\Models\FundingSource;
use App\Models\Scheme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
| Helpers shared by the feature tests.
*/

function admin(array $attributes = []): User
{
    return User::factory()->admin()->create($attributes);
}

function superAdmin(array $attributes = []): User
{
    return User::factory()->superAdmin()->create($attributes);
}

function dosen(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

/**
 * Valid Penelitian form payload; contributors default to the given lecturer as Ketua.
 *
 * @return array<string, mixed>
 */
function researchPayload(?User $lead = null, array $overrides = []): array
{
    return array_merge([
        'title' => 'Pengaruh Edukasi Gizi terhadap Status Gizi Balita di Pekanbaru',
        'year' => now()->year,
        'scheme_id' => Scheme::factory()->create()->id,
        'funding_source_id' => FundingSource::factory()->create()->id,
        'contributors' => contributorRows($lead),
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function communityServicePayload(?User $lead = null, array $overrides = []): array
{
    return array_merge([
        'title' => 'Penyuluhan Cuci Tangan Pakai Sabun di SD Negeri 12 Pekanbaru',
        'year' => now()->year,
        'scheme_id' => Scheme::factory()->communityService()->create()->id,
        'partner' => 'SD Negeri 12 Pekanbaru',
        'funding_source_id' => FundingSource::factory()->create()->id,
        'contributors' => contributorRows($lead),
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function publicationPayload(?User $lead = null, array $overrides = []): array
{
    return array_merge([
        'title' => 'Hubungan Pola Makan dengan Kejadian Gastritis pada Mahasiswa',
        'year' => now()->year,
        'venue_type' => 'JOURNAL',
        'venue_name' => 'Jurnal Kesehatan Masyarakat Riau',
        'level' => 'NATIONAL',
        'publisher' => 'Universitas PMC Press',
        'contributors' => contributorRows($lead),
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function bookPayload(?User $lead = null, array $overrides = []): array
{
    return array_merge([
        'title' => 'Buku Ajar Farmakologi Dasar',
        'year' => now()->year,
        'publisher' => 'Penerbit Medika Pekanbaru',
        'contributors' => contributorRows($lead),
    ], $overrides);
}

/**
 * @return array<string, mixed>
 */
function intellectualPropertyPayload(?User $lead = null, array $overrides = []): array
{
    return array_merge([
        'title' => 'Aplikasi Pemantauan Gizi Balita Berbasis Android',
        'year' => now()->year,
        'type' => 'COPYRIGHT',
        'contributors' => contributorRows($lead),
    ], $overrides);
}

/**
 * The given lecturer as first member, or one outside person.
 *
 * @return list<array<string, mixed>>
 */
function contributorRows(?User $lead): array
{
    return $lead ? [['user_id' => $lead->id]] : [['name' => 'Mitra Luar', 'affiliation' => 'Universitas X']];
}
