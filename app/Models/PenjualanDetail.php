<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    public function penjualan()
{
    return $this->belongsTo(Penjualan::class);
}

public function barang()
{
    return $this->belongsTo(Barang::class);
}

}
