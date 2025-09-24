<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPesanan extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'no_sp',
        'tanggal_sp',
        'supplier_id',
        'user_id',
        'keterangan',
        'sp_mode', // Ditambahkan kembali untuk kompatibilitas
        'jenis_sp', // Ditambahkan kembali untuk kompatibilitas
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_sp' => 'datetime',
    ];

    /**
     * Get the supplier that owns the surat pesanan.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user that created the surat pesanan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the details for the surat pesanan.
     */
    public function details()
    {
        return $this->hasMany(SuratPesananDetail::class);
    }

    /**
     * Mendefinisikan relasi one-to-one dengan Pembelian.
     * Sebuah Surat Pesanan memiliki satu Pembelian.
     */
    public function pembelian()
    {
        return $this->hasOne(Pembelian::class);
    }
}

