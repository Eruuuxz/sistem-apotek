<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
