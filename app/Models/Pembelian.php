<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelian';

    protected $fillable = [
        'no_faktur',
        'tanggal',
        'supplier_id',
        'total',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];
    
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PembelianDetail::class, 'pembelian_id');
    }
}
