<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPesananDetail extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan_detail';

    protected $fillable = [
        'surat_pesanan_id',
        'obat_id',
        'qty_pesan',
        'qty_terima',
        'harga_satuan',
    ];

    public function suratPesanan()
    {
        return $this->belongsTo(SuratPesanan::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class, 'obat_id');
    }
}
