<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift; // Pastikan model Shift sudah ada
use Carbon\Carbon;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Hapus shift yang sudah ada untuk menghindari duplikasi jika seeder dijalankan berkali-kali
        Shift::whereIn('name', ['Pagi', 'Sore'])->delete();

        // Shift Pagi: 08:00 - 16:00
        Shift::create([
            'name' => 'Pagi',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
        ]);

        // Shift Sore: 16:00 - 20:00 
        Shift::create([
            'name' => 'Sore',
            'start_time' => '16:00:00',
            'end_time' => '20:00:00', 
        ]);

        $this->command->info('Default shifts (Pagi, Sore) seeded successfully!');
    }
}
