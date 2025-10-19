<?php

namespace Database\Seeders\Production;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cabangPusat = Cabang::where('is_pusat', true)->first();

        if (!$cabangPusat) {
            $this->command->error('❌ Cabang Pusat tidak ditemukan! Jalankan CabangSeeder dulu.');
            return;
        }

        // Buat admin default
        $admin = User::firstOrCreate(
            ['email' => 'admin@apotek.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'), // ⚠️ Password sementara
                'role' => 'admin',
                'cabang_id' => $cabangPusat->id,
            ]
        );

        $this->command->info('✅ Admin user created');
        $this->command->warn('📧 Email: admin@apotek.local');
        $this->command->warn('🔑 Password: admin123');
        $this->command->error('⚠️  WAJIB GANTI PASSWORD SETELAH LOGIN PERTAMA!');
    }
}