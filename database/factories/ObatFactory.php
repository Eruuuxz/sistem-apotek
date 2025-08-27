<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Obat; // Import model Obat
use App\Models\Supplier; // Import model Supplier

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

        // Pastikan ada supplier di database
        $supplierId = Supplier::inRandomOrder()->first()->id ?? Supplier::factory()->create()->id;

        return [
            'kode' => 'OBT' . $this->faker->unique()->numerify('###'),
            'nama' => $this->faker->word() . ' ' . $this->faker->randomElement(['Tablet', 'Sirup', 'Kapsul']),
            'kategori' => $this->faker->randomElement(['Obat Bebas', 'Obat Bebas Terbatas', 'Obat Keras', 'Psikotropika']),
            'stok' => $this->faker->numberBetween(10, 100),
            'min_stok' => $this->faker->numberBetween(3, 10), // Tambahkan min_stok
            'harga_dasar' => $hargaDasar,
            'persen_untung' => $persenUntung,
            'harga_jual' => $hargaJual,
            'supplier_id' => $supplierId,
        ];
    }
}
