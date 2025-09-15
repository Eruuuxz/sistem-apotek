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
        'sediaan', 
        'kemasan_besar', 
        'satuan_terkecil', 
        'rasio_konversi', 
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

    // Accessor untuk mendapatkan stok dalam format kemasan besar (jika ada)
    public function getStokFormattedAttribute()
    {
        if ($this->kemasan_besar && $this->rasio_konversi > 1) {
            $jumlahKemasan = floor($this->stok / $this->rasio_konversi);
            $sisaSatuan = $this->stok % $this->rasio_konversi;
            
            $formatted = '';
            if ($jumlahKemasan > 0) {
                $formatted .= "{$jumlahKemasan} {$this->kemasan_besar}";
            }
            if ($sisaSatuan > 0) {
                if ($formatted !== '') {
                    $formatted .= " dan ";
                }
                $formatted .= "{$sisaSatuan} {$this->satuan_terkecil}";
            }
            return $formatted === '' ? "0 {$this->satuan_terkecil}" : $formatted;
        }
        return "{$this->stok} {$this->satuan_terkecil}";
    }
}