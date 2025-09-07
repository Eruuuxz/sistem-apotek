<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@apotek.local'], // Kriteria pencarian
            [
                'name' => 'Admin Apotek',
                'password' => Hash::make('admin12345'), // Ganti dengan password yang kuat!
                'role' => 'admin',
            ]
        );
    }
}