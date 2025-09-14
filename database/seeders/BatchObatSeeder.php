<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Obat;
use App\Models\Supplier;
use App\Models\BatchObat;
use Carbon\Carbon;

class BatchObatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada obat dan supplier terlebih dahulu
        $obats = Obat::all();
        $suppliers = Supplier::all();

        if ($obats->isEmpty()) {
            $this->call(ObatSeeder::class);
            $obats = Obat::all();
        }

        if ($suppliers->isEmpty()) {
            $this->call(SupplierSeeder::class);
            $suppliers = Supplier::all();
        }

        // Hapus data batch_obat yang sudah ada untuk menghindari duplikasi saat re-seeding
        BatchObat::truncate();

        foreach ($obats as $obat) {
            // Buat beberapa batch untuk setiap obat
            for ($i = 0; $i < rand(1, 3); $i++) { // Setiap obat memiliki 1-3 batch
                $stokAwal = rand(20, 200);
                $hargaBeliPerUnit = $obat->harga_dasar * (1 - rand(5, 20) / 100); // Harga beli lebih rendah dari harga dasar
                $expiredDate = Carbon::now()->addMonths(rand(3, 24)); // Kedaluwarsa 3 bulan hingga 2 tahun dari sekarang

                BatchObat::create([
                    'obat_id' => $obat->id,
                    'no_batch' => 'BATCH-' . $obat->kode . '-' . ($i + 1) . '-' . $this->generateRandomString(4),
                    'expired_date' => $expiredDate,
                    'stok_awal' => $stokAwal,
                    'stok_saat_ini' => $stokAwal,
                    'harga_beli_per_unit' => $hargaBeliPerUnit,
                    'supplier_id' => $suppliers->random()->id,
                ]);
            }
        }

        // Update total stok di tabel 'obat' berdasarkan batch_obat
        foreach ($obats as $obat) {
            $totalStokDariBatch = BatchObat::where('obat_id', $obat->id)->sum('stok_saat_ini');
            $obat->stok = $totalStokDariBatch;
            $obat->save();
        }

        $this->command->info('BatchObat seeded successfully!');
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}