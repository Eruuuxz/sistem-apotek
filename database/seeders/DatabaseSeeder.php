<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\Production\ProductionSeeder;
use Database\Seeders\Development\DevelopmentSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * * Logic:
     * - Production: HANYA ProductionSeeder (minimal data)
     * - Development: ProductionSeeder + DevelopmentSeeder (dengan dummy)
     */
    public function run(): void
    {
        // 1. WAJIB: Seed data production (admin + struktur)
        $this->call(ProductionSeeder::class);

        // 2. OPTIONAL: Seed dummy data (hanya di local/development)
        if (app()->environment('local', 'development')) {
            $this->command->newLine();
            $this->command->warn('🔧 Environment: ' . app()->environment());
            $this->command->warn('💾 Seeding dummy data for development...');
                        
            // Pastikan DevelopmentSeeder memanggil seeder yang sudah di-move dan namespace-nya diupdate
            $this->call(DevelopmentSeeder::class);
        } else {
            $this->command->newLine();
            $this->command->info('🏥 Environment: ' . app()->environment());
            $this->command->info('✅ Production seeding only (no dummy data)');
        }
    }
}