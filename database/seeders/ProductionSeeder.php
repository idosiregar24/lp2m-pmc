<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Production bootstrap: sample masters + one admin whose password comes from
 * ADMIN_INITIAL_PASSWORD and must be changed on first login.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $password = config('bp2m.admin_initial_password');

        if (blank($password) || mb_strlen($password) < 8) {
            throw new RuntimeException('Isi ADMIN_INITIAL_PASSWORD (minimal 8 karakter) di .env sebelum menjalankan ProductionSeeder.');
        }

        $this->call(MasterDataSeeder::class);

        User::query()->firstOrCreate(['username' => 'admin'], [
            'name' => 'Super Admin BP2M',
            'password' => $password,
            'role' => UserRole::SuperAdmin,
            'is_active' => true,
            'must_change_password' => true,
        ]);
    }
}
