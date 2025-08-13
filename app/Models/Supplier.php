<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Tambahkan ini

class Supplier extends Model
{
    use HasFactory; // Tambahkan ini

    protected $table = 'supplier';
    protected $fillable = ['kode', 'nama', 'alamat', 'kota', 'telepon'];
    
    public function barang()
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }

    public function obat()
    {
        // Asumsi obat juga bisa punya supplier, jika tidak relevan bisa dihapus
        return $this->hasMany(Obat::class, 'supplier_id'); 
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}

