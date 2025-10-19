<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // HANYA shift default (minimal 2 shift)
        $shifts = [
            ['name' => 'Pagi', 'start_time' => '08:00:00', 'end_time' => '16:00:00'],
            ['name' => 'Sore', 'start_time' => '16:00:00', 'end_time' => '20:00:00'],
        ];

        foreach ($shifts as $shift) {
            Shift::firstOrCreate(
                ['name' => $shift['name']],
                $shift
            );
        }
        $this->command->info('✅ Default Shifts created (Pagi, Sore)');
    }
}