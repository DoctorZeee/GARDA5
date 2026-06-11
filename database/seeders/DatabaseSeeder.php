<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Production-safe DatabaseSeeder.
 *
 * - Only seeds master/reference data (Wilayah, Videos) in all environments.
 * - Dev fixtures (demo accounts) are gated behind !isProduction().
 * - For production admin creation, use: php artisan app:create-admin
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Always safe: master data
        $this->call(WilayahSeeder::class);
        $this->call(VideoSeeder::class);

        // Dev-only fixtures: demo accounts with predictable passwords
        if (! app()->isProduction()) {
            $this->call(DevFixtureSeeder::class);
        }
    }
}
