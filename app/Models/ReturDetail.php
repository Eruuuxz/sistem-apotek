<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturDetail extends Model
{
    public function retur()
{
    return $this->belongsTo(Retur::class);
}

public function barang()
{
    return $this->belongsTo(Barang::class);
}

}
