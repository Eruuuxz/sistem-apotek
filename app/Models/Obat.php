<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    protected $table = 'obat';
    protected $fillable = ['kode', 'nama', 'kategori', 'stok', 'harga_dasar', 'persen_untung', 'harga_jual'];
}
