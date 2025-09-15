<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_tindakan',
        'biaya_dasar',
    ];

    protected $casts = [
        'biaya_dasar' => 'decimal:2',
    ];

    public function consultations()
    {
        return $this->belongsToMany(Consultation::class, 'consultation_medical_action')
                    ->withPivot('biaya_tindakan_override')
                    ->withTimestamps();
    }
}
