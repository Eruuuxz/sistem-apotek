<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';

    protected $fillable = [
        'nama',
        'telepon',
        'alamat',
        'tipe', // Menambahkan 'tipe'
        'no_ktp',
        'file_ktp',
    ];

    /**
     * Relasi ke penjualan untuk riwayat pembelian.
     */
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}