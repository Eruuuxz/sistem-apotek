<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $table = 'shifts'; // Nama tabel untuk definisi shift

    protected $fillable = [
        'name',        // Nama shift (e.g., Pagi, Siang, Malam)
        'start_time',  // Waktu mulai shift (e.g., 08:00:00)
        'end_time',    // Waktu berakhir shift (e.g., 16:00:00)
    ];

    // Relasi ke CashierShift
    public function cashierShifts()
    {
        return $this->hasMany(CashierShift::class);
    }
}
