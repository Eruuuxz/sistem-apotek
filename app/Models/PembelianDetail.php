<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'pembelian_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }
}
