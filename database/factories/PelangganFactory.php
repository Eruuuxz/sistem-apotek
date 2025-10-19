<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PelangganFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Pelanggan::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->name(),
            'telepon' => $this->faker->phoneNumber(),
            'alamat' => $this->faker->address(),
            'no_ktp' => $this->faker->unique()->numerify('################'), // 16 digit angka
            'file_ktp' => null, // Untuk dummy, biarkan null atau tambahkan logika jika ingin dummy file
            // 'status_member' => $this->faker->randomElement(['member', 'non_member']),
            // 'point' => $this->faker->numberBetween(0, 1000),
        ];
    }
}