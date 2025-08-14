<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturDetail extends Model
{
    use HasFactory;

    protected $table = 'retur_detail';
    
    protected $fillable = [
        'retur_id',
        'barang_id',
        'qty',
        'harga',
        'subtotal',
    ];

    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'retur_id');
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
