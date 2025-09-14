<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratPesanan extends Model
{
    use HasFactory;

    protected $table = 'surat_pesanan';

    protected $fillable = [
        'no_sp',
        'tanggal_sp',
        'supplier_id',
        'user_id',
        'file_template',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_sp' => 'datetime',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SuratPesananDetail::class);
    }
}
