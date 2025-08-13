<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $table = 'supplier';
    protected $fillable = ['kode', 'nama', 'alamat', 'kota', 'telepon'];
    
    public function barang()
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }

    public function obat()
    {
        return $this->hasMany(Obat::class);
    }
}