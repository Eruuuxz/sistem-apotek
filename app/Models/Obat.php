<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Obat extends Model
{
    use HasFactory;

    protected $table = 'obat';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'is_psikotropika',
        'stok',
        'min_stok',
        'expired_date',
        'harga_dasar',
        'persen_untung',
        'harga_jual',
        'supplier_id',
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class);
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class);
    }

    public function returDetails()
    {
        return $this->hasMany(ReturDetail::class);
    }

    public function suratPesananDetails() // Tambahkan relasi ini
    {
        return $this->hasMany(SuratPesananDetail::class);
    }
}