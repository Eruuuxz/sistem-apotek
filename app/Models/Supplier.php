<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'kota',
        'telepon',
    ];

    // UBAH INI: dari 'obat' menjadi 'obats'
    public function obats()
    {
        return $this->hasMany(Obat::class);
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }

    public function suratPesanans()
    {
        return $this->hasMany(SuratPesanan::class);
    }

    // Relasi untuk mengambil retur pembelian
    public function returPembelian()
    {
        // Asumsi: 'pembelian' adalah nama relasi di model Retur
        return $this->hasManyThrough(Retur::class, Pembelian::class)
                    ->where('retur.jenis', 'pembelian');
    }
}