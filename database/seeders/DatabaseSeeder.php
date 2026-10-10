<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;

/**
 * Demo data for local/staging. Production uses ProductionSeeder.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('DatabaseSeeder berisi data demo — gunakan ProductionSeeder di production.');
        }

        $this->call([
            MasterDataSeeder::class,
            UserSeeder::class,
            DemoDataSeeder::class,
        ]);
    }
}
