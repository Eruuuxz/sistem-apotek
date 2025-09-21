<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashierShift extends Model
{
    use HasFactory;

    protected $table = 'cashier_shifts'; // Nama tabel untuk sesi shift kasir

    protected $fillable = [
        'user_id',        // ID kasir yang menjalankan shift
        'shift_id',       // ID definisi shift (Pagi, Siang, Malam)
        'start_time',     // Waktu mulai shift kasir (datetime)
        'end_time',       // Waktu berakhir shift kasir (datetime, nullable)
        'initial_cash',   // Modal awal kasir saat memulai shift
        'final_cash',     // Uang akhir kasir saat mengakhiri shift (nullable)
        'total_sales',    // Total penjualan selama shift ini (dihitung saat shift berakhir)
        'status',         // Status shift (e.g., 'open', 'closed')
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Relasi ke User (kasir)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Shift (definisi shift)
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    // Relasi ke Penjualan (transaksi yang terjadi selama shift ini)
    public function sales()
    {
        return $this->hasMany(Penjualan::class, 'cashier_shift_id');
    }
    public function penjualan()
{
    return $this->hasMany(Penjualan::class);
}
}
