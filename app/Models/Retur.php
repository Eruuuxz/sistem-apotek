<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';

    protected $fillable = [
        'no_retur',
        'tanggal',
        'jenis',
        'transaksi_id',
        'total',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(ReturDetail::class);
    }

    // Relasi ke transaksi sumber
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'transaksi_id')->where('jenis', 'pembelian');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'transaksi_id')->where('jenis', 'penjualan');
    }
}
