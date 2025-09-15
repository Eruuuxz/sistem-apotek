<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelanggan_id',
        'doctor_name',
        'tanggal_konsultasi',
        'biaya_konsultasi',
        'total_biaya',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_konsultasi' => 'datetime',
        'biaya_konsultasi' => 'decimal:2',
        'total_biaya' => 'decimal:2',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    public function obats()
    {
        return $this->belongsToMany(Obat::class, 'consultation_obat')
                    ->withPivot('qty', 'harga_satuan', 'subtotal')
                    ->withTimestamps();
    }

    public function medicalActions()
    {
        return $this->belongsToMany(MedicalAction::class, 'consultation_medical_action')
                    ->withPivot('biaya_tindakan_override')
                    ->withTimestamps();
    }
}

