<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Shift::firstOrCreate(
            ['name' => 'Pagi'],
            ['start_time' => '08:00:00', 'end_time' => '16:00:00']
        );
        Shift::firstOrCreate(
            ['name' => 'Siang'],
            ['start_time' => '16:00:00', 'end_time' => '22:00:00']
        );
        Shift::firstOrCreate(
            ['name' => 'Malam'],
            ['start_time' => '22:00:00', 'end_time' => '08:00:00']
        );
    }
}