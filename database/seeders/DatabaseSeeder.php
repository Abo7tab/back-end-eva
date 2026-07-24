<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Always seed in every environment
        $this->call([
            SiteSettingsSeeder::class,
            AdminSeeder::class,
            WhatsAppNumberSeeder::class,
        ]);
        
        // Test data ONLY in local/development environment
        if (app()->environment('local', 'development')) {
            $this->call([
                BrandSeeder::class,
                CategorySeeder::class,
                ProductSeeder::class,
            ]);
        }
    }
}
