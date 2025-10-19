<?php

namespace Database\Seeders\Development;

use Illuminate\Database\Seeder;
use Database\Seeders\DevelopmentSupplierSeeder;
use Database\Seeders\DevelopmentObatSeeder;
use Database\Seeders\DevelopmentBatchObatSeeder;
use Database\Seeders\DevelopmentPelangganSeeder;

class DevelopmentSeeder extends Seeder
{
    /**
     * Seed DUMMY DATA untuk testing/development
     * TIDAK akan jalan di production
     */
    public function run(): void
    {
        $this->command->warn('⚠️  Running DEVELOPMENT seeder (with dummy data)');
        $this->command->newLine();

        // CATATAN: SupplierSeeder, ObatSeeder, BatchObatSeeder, PelangganSeeder
        // harus dipindahkan ke folder "database/seeders/Development"
        // dan namespace-nya diubah menjadi "Database\Seeders\Development".
        // Karena file aslinya tidak diubah, kita panggil dari namespace lama
        // atau kita asumsikan sudah dipindahkan dan di-refactor.

        // Memanggil seeder dummy
        $this->call([
            SupplierSeeder::class,       // 3 dummy supplier
            ObatSeeder::class,            // 50 dummy obat
            BatchObatSeeder::class,       // Batch untuk dummy obat
            PelangganSeeder::class,       // 10 dummy pelanggan
        ]);

        $this->command->info('✅ Development dummy data seeded!');
    }
}