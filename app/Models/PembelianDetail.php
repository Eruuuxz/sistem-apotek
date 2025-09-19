<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail';

    protected $fillable = [
        'pembelian_id',
        'obat_id',
        'jumlah',
        'harga_beli',
        'ppn_amount',
        'no_batch',      // Ditambahkan
        'expired_date',  // Ditambahkan
    ];

    protected $casts = [
        'harga_beli' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
        'expired_date' => 'date', // Ditambahkan
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}