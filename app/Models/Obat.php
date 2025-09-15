<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'ppn_included',
        'ppn_rate',
    ];

    protected $casts = [
        'expired_date' => 'date',
        'harga_dasar' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'ppn_included' => 'boolean',
        'ppn_rate' => 'decimal:2',
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

    public function suratPesananDetails()
    {
        return $this->hasMany(SuratPesananDetail::class);
    }

    public function consultations()
    {
        return $this->belongsToMany(Consultation::class, 'consultation_obat')
                    ->withPivot('qty', 'harga_satuan', 'subtotal')
                    ->withTimestamps();
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

    // Accessor untuk mendapatkan harga jual tanpa PPN (jika PPN included)
    public function getHargaJualTanpaPpnAttribute()
    {
        if ($this->ppn_included && $this->ppn_rate > 0) {
            return $this->harga_jual / (1 + $this->ppn_rate / 100);
        }
        return $this->harga_jual;
    }

    // Accessor untuk mendapatkan harga dasar tanpa PPN (jika PPN included)
    public function getHargaDasarTanpaPpnAttribute()
    {
        if ($this->ppn_included && $this->ppn_rate > 0) {
            return $this->harga_dasar / (1 + $this->ppn_rate / 100);
        }
        return $this->harga_dasar;
    }
}