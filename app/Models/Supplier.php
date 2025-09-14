<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'kota',
        'telepon',
    ];

    public function obat()
    {
        return $this->hasMany(Obat::class);
    }

    public function pembelian() // Tambahkan relasi ini
    {
        return $this->hasMany(Pembelian::class);
    }

    public function suratPesanans() // Tambahkan relasi ini
    {
        return $this->hasMany(SuratPesanan::class);
    }

    // Relasi retur tidak langsung ke supplier, tapi melalui pembelian.
    // Jika ingin langsung, perlu kolom supplier_id di tabel retur atau relasi hasManyThrough
    // Untuk saat ini, kita akan mengambilnya melalui Pembelian di SupplierController@show
}
