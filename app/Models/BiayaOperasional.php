<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaOperasional extends Model
{
    use HasFactory;

    protected $table = 'biaya_operasional';

    protected $fillable = [
        'tanggal',
        'jenis_biaya',
        'keterangan',
        'jumlah',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'tanggal' => 'date', // Ini akan mengubah kolom tanggal menjadi objek Carbon secara otomatis
        'jumlah' => 'decimal:2',
    ];
}