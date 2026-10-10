<?php

namespace Database\Factories;

use App\Models\FundingSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FundingSource>
 */
class FundingSourceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => 'Dana '.fake()->unique()->words(2, true),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
