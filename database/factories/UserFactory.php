<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\StudyProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * A lecturer by default.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nidn_nuptk' => fake()->unique()->numerify('##########'),
            'study_program_id' => StudyProgram::factory(),
            'username' => fake()->unique()->regexify('[a-z]{6}[0-9]{3}'),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::Dosen,
            'is_active' => true,
            'must_change_password' => false,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => UserRole::Admin,
            'nidn_nuptk' => null,
            'study_program_id' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function mustChangePassword(): static
    {
        return $this->state(fn () => ['must_change_password' => true]);
    }
}
