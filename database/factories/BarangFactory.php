<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Barang>
 */

class BarangFactory extends Factory
{
    protected $model = \App\Models\Barang::class;

    public function definition(): array
    {
        return [
            'kode' => 'BRG' . $this->faker->unique()->numerify('###'),
            'nama' => $this->faker->word(),
            'harga_jual' => $this->faker->numberBetween(5000, 50000),
            'stok' => $this->faker->numberBetween(10, 100),
        ];
    }
}
