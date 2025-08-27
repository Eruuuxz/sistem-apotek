<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Supplier; // Import model Supplier

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Supplier::create([
            'kode' => 'SUP001',
            'nama' => 'PT. Pharma Jaya',
            'alamat' => 'Jl. Merdeka No. 1',
            'kota' => 'Jakarta',
            'telepon' => '021123456',
        ]);

        Supplier::create([
            'kode' => 'SUP002',
            'nama' => 'CV. Medika Sejahtera',
            'alamat' => 'Jl. Kesehatan No. 5',
            'kota' => 'Bandung',
            'telepon' => '022987654',
        ]);

        Supplier::create([
            'kode' => 'SUP003',
            'nama' => 'PT. Obat Kuat',
            'alamat' => 'Jl. Maju Mundur No. 10',
            'kota' => 'Surabaya',
            'telepon' => '031112233',
        ]);
        // Nanti bisa menambahkan lebih banyak data dummy jika diperlukan
    }
}
