<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';
    
    protected $fillable = [
        'kode',
        'nama',
        'harga_jual',
        'stok',
        'supplier_id',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pembelianDetails(): HasMany
    {
        return $this->hasMany(PembelianDetail::class, 'barang_id');
    }

    public function penjualanDetails(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'barang_id');
    }
}
