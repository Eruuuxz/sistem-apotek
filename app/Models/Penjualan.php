<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';
    
    protected $fillable = [
        'no_nota',
        'tanggal',
        'kasir_nama',
        'total',
        'bayar',
        'kembalian',
    ];

    public function detail(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'penjualan_id');
    }
}
