<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'kode',
        'nama',
        'kota',
        'alamat',
        'telepon',
    ];

    public function obat(): HasMany
    {
        return $this->hasMany(Obat::class, 'supplier_id');
    }

    public function barang(): HasMany
    {
        return $this->hasMany(Barang::class, 'supplier_id');
    }

    public function pembelian(): HasMany
    {
        return $this->hasMany(Pembelian::class, 'supplier_id');
    }
}
