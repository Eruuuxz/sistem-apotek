<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BatchObat extends Model
{
    use HasFactory;

    protected $table = 'batch_obat';

    protected $fillable = [
        'obat_id',
        'no_batch',
        'expired_date',
        'stok_awal',
        'stok_saat_ini',
        'harga_beli_per_unit',
        'supplier_id'
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}