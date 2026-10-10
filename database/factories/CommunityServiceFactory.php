<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\CommunityService;
use App\Models\FundingSource;
use App\Models\Scheme;
use App\Models\User;
use Database\Factories\Concerns\ActivityStates;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommunityService>
 */
class CommunityServiceFactory extends Factory
{
    use ActivityStates;

    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(10), '.'),
            'year' => fake()->numberBetween(now()->year - 5, now()->year),
            'scheme_id' => Scheme::factory()->communityService(),
            'partner' => 'Kelompok Masyarakat '.fake()->city(),
            'funding_source_id' => FundingSource::factory(),
            'created_by' => User::factory(),
            'status' => VerificationStatus::Draft,
        ];
    }
}
