<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'no_faktur',
        'no_faktur_pbf', 
        'tanggal',
        'supplier_id',
        'surat_pesanan_id', 
        'total',
        'diskon', // Pastikan ini ada jika digunakan
        'diskon_type', // Pastikan ini ada jika digunakan
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function suratPesanan() // Tambahkan relasi ini
    {
        return $this->belongsTo(SuratPesanan::class);
    }

    public function detail()
    {
        return $this->hasMany(PembelianDetail::class);
    }
}
