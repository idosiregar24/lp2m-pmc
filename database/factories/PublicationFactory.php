<?php

namespace Database\Factories;

use App\Enums\PublicationLevel;
use App\Enums\VenueType;
use App\Enums\VerificationStatus;
use App\Models\Publication;
use App\Models\User;
use Database\Factories\Concerns\ActivityStates;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Publication>
 */
class PublicationFactory extends Factory
{
    use ActivityStates;

    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(10), '.'),
            'year' => fake()->numberBetween(now()->year - 5, now()->year),
            'venue_type' => fake()->randomElement(VenueType::cases()),
            'venue_name' => 'Jurnal '.fake()->words(3, true),
            'level' => fake()->randomElement(PublicationLevel::cases()),
            'publisher' => fake()->company(),
            'created_by' => User::factory(),
            'status' => VerificationStatus::Draft,
        ];
    }
}
