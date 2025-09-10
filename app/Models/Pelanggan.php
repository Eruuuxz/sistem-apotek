<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan'; // Menentukan nama tabel secara eksplisit

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'no_ktp',
        'file_ktp',
        'status_member',
        'point',
    ];

    // Jika ada relasi di masa depan, bisa ditambahkan di sini
    // Contoh: relasi ke Penjualan jika pelanggan bisa direlasikan ke transaksi
    // public function penjualan()
    // {
    //     return $this->hasMany(Penjualan::class);
    // }
}
