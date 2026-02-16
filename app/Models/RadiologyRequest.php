<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadiologyRequest extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'prescription_id',
        'visit_id',
        'test_name',
        'clinical_notes',
        'priority',
        'status',
        'report',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function prescription()
    {
        return $this->belongsTo(Prescription::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(RadiologyStaff::class, 'completed_by');
    }

    public function files()
    {
        return $this->morphMany(RequestFile::class, 'request');
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
