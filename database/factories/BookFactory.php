<?php

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\Book;
use App\Models\User;
use Database\Factories\Concerns\ActivityStates;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    use ActivityStates;

    public function definition(): array
    {
        return [
            'title' => rtrim(fake()->sentence(10), '.'),
            'year' => fake()->numberBetween(now()->year - 5, now()->year),
            'publisher' => fake()->company(),
            'created_by' => User::factory(),
            'status' => VerificationStatus::Draft,
        ];
    }
}
