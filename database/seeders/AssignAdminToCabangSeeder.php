<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cabang;
class AssignAdminToCabangSeeder extends Seeder
{
    public function run()
    {
        $cabangPusat = Cabang::where('is_pusat', true)->first();
        if ($cabangPusat) {
            $admin = User::where('email', 'admin@apotek.local')->first();
            if ($admin) {
                $admin->cabang_id = $cabangPusat->id;
                $admin->save();
            }
        }
    }
}
