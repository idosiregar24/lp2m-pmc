<?php

namespace Database\Factories;

use App\Enums\StudyProgramLevel;
use App\Models\Faculty;
use App\Models\StudyProgram;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StudyProgram>
 */
class StudyProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'faculty_id' => Faculty::factory(),
            'name' => ucwords(fake()->unique()->words(2, true)),
            'level' => fake()->randomElement([StudyProgramLevel::D3, StudyProgramLevel::S1]),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
