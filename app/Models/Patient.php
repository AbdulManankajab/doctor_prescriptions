<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_number',
        'name',
        'age',
        'gender',
        'phone',
        'address',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->patient_number)) {
                $patient->patient_number = 'P' . str_pad(self::max('id') + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function prescriptions()
    {
        return $this->hasMany(Prescription::class);
    }

    public function allergies()
    {
        return $this->hasMany(PatientAllergy::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function diagnoses()
    {
        return $this->hasMany(Diagnosis::class);
    }
}
