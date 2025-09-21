<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Obat; // Import model Obat
use App\Models\Supplier; // Import model Supplier
use Carbon\Carbon; // Import Carbon

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Obat>
 */
class ObatFactory extends Factory
{
    protected $model = Obat::class; // Tentukan model yang digunakan

    public function definition(): array
    {
        $hargaDasar = $this->faker->numberBetween(5000, 50000);
        $persenUntung = $this->faker->randomFloat(2, 10, 50); // 10% - 50%
        $hargaJual = $hargaDasar + ($hargaDasar * $persenUntung / 100);

        // Tentukan PPN Rate secara acak, misalnya 0% atau 11%
        $ppnRate = $this->faker->boolean(70) ? 11.00 : 0.00; // 70% kemungkinan ada PPN 11%

        // Tentukan apakah PPN sudah termasuk harga jual
        $ppnIncluded = false;
        if ($ppnRate > 0) {
            $ppnIncluded = $this->faker->boolean(50); // 50% kemungkinan harga sudah termasuk PPN jika ada PPN
        }

        // Jika PPN included, sesuaikan harga jual agar PPN bisa diekstrak
        // Logika perhitungan PPN dari hargaJual akan ada di controller POS.
        // Asumsi harga jual yang disimpan adalah harga final yang harus dibayar pelanggan.
        // Jika PPN included, kita tambahkan PPN ke harga jual.
        if ($ppnIncluded) {
            $hargaJual = $hargaJual * (1 + $ppnRate / 100);
        }

        // Pastikan ada supplier di database
        $supplierId = Supplier::inRandomOrder()->first()->id ?? Supplier::factory()->create()->id;

        return [
            'kode' => 'OBT' . $this->faker->unique()->numerify('###'),
            'nama' => $this->faker->word() . ' ' . $this->faker->randomElement(['Tablet', 'Sirup', 'Kapsul']),
            'kategori' => $this->faker->randomElement(['Obat Bebas', 'Obat Bebas Terbatas', 'Obat Keras', 'Psikotropika']),
            'stok' => 0, // Set stok awal ke 0, nanti akan diupdate oleh BatchObatSeeder
            'min_stok' => $this->faker->numberBetween(3, 10),
            'harga_dasar' => $hargaDasar,
            'persen_untung' => $persenUntung,
            'harga_jual' => $hargaJual,
            'supplier_id' => $supplierId,
            'expired_date' => Carbon::now()->addMonths(rand(6, 36)), // Tambahkan expired_date di sini juga
            'is_psikotropika' => $this->faker->boolean(10), // 10% kemungkinan psikotropika
            'ppn_included' => $ppnIncluded, // Tambahkan ini
            'ppn_rate' => $ppnRate, // Tambahkan ini
        ];
    }
}
