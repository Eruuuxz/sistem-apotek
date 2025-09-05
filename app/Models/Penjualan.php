<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualan';

    protected $fillable = [
        'no_nota',
        'tanggal',
        'kasir_nama',
        'user_id',
        'total',
        'bayar',
        'kembalian',
    ];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(PenjualanDetail::class, 'penjualan_id');
    }
    // Disini perbarui relasi kasir() untuk menggunakan user_id
    public function kasir(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}