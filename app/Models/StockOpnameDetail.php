<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_opname_id',
        'obat_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'tipe_penyesuaian',
        'catatan_detail',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function obat()
    {
        return $this->belongsTo(Obat::class);
    }
}
