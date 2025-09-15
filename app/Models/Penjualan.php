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
        'user_id',
        'cashier_shift_id', 
        'cabang_id',
        'total',
        'bayar',
        'kembalian',
        'nama_pelanggan',    
        'alamat_pelanggan',  
        'telepon_pelanggan', 
        'diskon_type',
        'diskon_value',
        'diskon_amount',
        'pelanggan_id', 
        'ppn_percent', 
        'ppn_amount',  
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'ppn_percent' => 'decimal:2',
        'ppn_amount' => 'decimal:2',
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
    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cabang_id');
    } 
    public function pelanggan(): BelongsTo 
    {
        return $this->belongsTo(Pelanggan::class, 'pelanggan_id');
    }
    public function getSubtotalAttribute()
    {
        return $this->total + $this->diskon_amount;
    }

    // Relasi ke CashierShift
    public function cashierShift()
    {
        return $this->belongsTo(CashierShift::class);
    }
}
