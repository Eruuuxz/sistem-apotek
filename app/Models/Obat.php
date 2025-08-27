<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';
    
    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'stok',
        'min_stok', 
        'harga_dasar',
        'persen_untung',
        'harga_jual',
        'supplier_id',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function pembelianDetails(): HasMany
    {
        return $this->hasMany(PembelianDetail::class, 'obat_id');
    }

    // Tambahkan relasi untuk PenjualanDetail dan ReturDetail
    public function penjualanDetails(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'obat_id'); // Akan kita ubah di migrasi penjualan_detail
    }

    public function returDetails(): HasMany
    {
        return $this->hasMany(ReturDetail::class, 'obat_id'); // Akan kita ubah di migrasi retur_detail
    }
}
