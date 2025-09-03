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
        'obat_id',
        'qty',
        'harga',
        'subtotal',
    ];

    public function retur(): BelongsTo
    {
        return $this->belongsTo(Retur::class, 'retur_id');
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}
