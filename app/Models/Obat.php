<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini

class Obat extends Model
{
    use HasFactory; // Tambahkan ini

    protected $table = 'obat';
    protected $fillable = ['kode', 'nama', 'kategori', 'stok', 'harga_dasar', 'persen_untung', 'harga_jual', 'supplier_id']; // Tambahkan supplier_id
    
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}

