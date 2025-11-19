<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;

class ProductionSeeder extends Seeder
{
    /**
     * Seed data MINIMAL untuk production
     * Tidak ada data dummy, hanya struktur dasar
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Production Seeder...');
        $this->command->newLine();

        // 1. Cabang (wajib ada sebelum user)
        $this->call(CabangSeeder::class);

        // 2. Admin user (untuk login pertama kali)
        $this->call(AdminUserSeeder::class);

        $this->command->newLine();
        $this->command->info('✅ Production seeding completed!');
        $this->command->info('🏥 Sistem siap digunakan oleh pihak apotek');
        $this->command->newLine();

        // Instruksi setup
        $this->command->line('📋 NEXT STEPS:');
        $this->command->line('1. Login dengan: admin@apotek.local / admin123');
        $this->command->line('2. Ganti password di menu Profile');
        $this->command->line('3. Update data apotek di menu Master Data > Cabang');
        $this->command->line('4. Tambah user kasir di menu Management User');
        $this->command->line('5. Mulai input data: Supplier → Obat → dll');
    }
}