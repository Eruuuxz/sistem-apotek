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
}
