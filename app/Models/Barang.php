<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    protected $fillable = ['kode', 'nama', 'harga_jual', 'stok'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }


    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}
