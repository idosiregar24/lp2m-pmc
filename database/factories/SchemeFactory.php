<?php

namespace Database\Factories;

use App\Enums\SchemeType;
use App\Models\Scheme;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Scheme>
 */
class SchemeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => SchemeType::Research,
            'name' => 'Skema '.fake()->unique()->words(3, true),
            'is_active' => true,
        ];
    }

    public function communityService(): static
    {
        return $this->state(fn () => ['type' => SchemeType::CommunityService]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
