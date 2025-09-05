<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retur extends Model
{
    use HasFactory;

    protected $table = 'retur';

    protected $fillable = [
        'no_retur',
        'tanggal',
        'jenis',
        'transaksi_id',
        'total',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'datetime', // Tambahkan baris ini
    ];

    public function details(): HasMany
    {
        return $this->hasMany(ReturDetail::class, 'retur_id');
    }
}