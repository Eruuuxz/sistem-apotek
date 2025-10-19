<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\Cabang;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // HANYA cabang pusat (wajib untuk sistem berjalan)
        Cabang::firstOrCreate(
            ['kode' => 'PUSAT'],
            [
                'nama' => 'Apotek Pusat',
                'alamat' => 'Silakan update alamat di menu Master Data',
                'is_pusat' => true,
            ]
        );
        $this->command->info('✅ Cabang Pusat created');
    }
}