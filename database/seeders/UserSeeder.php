<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Super Admin account seeder for LP2M Universitas Pekanbaru Medical Center.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Super Admin LP2M',
                'password' => Hash::make('password'),
                'role' => UserRole::SuperAdmin,
                'is_active' => true,
                'must_change_password' => false,
            ]
        );
    }
}
