<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Prescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'prescription_number',
        'qr_token',
        'patient_id',
        'examination_id',
        'diagnosis_id',
        'diagnosis',
        'notes',
        'facility_snapshot',
        'status',
        'sent_at',
        'dispensed_at',
        'dispensed_by',
        'visit_id',
    ];

    protected $casts = [
        'facility_snapshot' => 'array',
        'sent_at' => 'datetime',
        'dispensed_at' => 'datetime',
    ];

    public function dispensedBy()
    {
        return $this->belongsTo(PharmacyUser::class, 'dispensed_by');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($prescription) {
            if (empty($prescription->prescription_number)) {
                $prescription->prescription_number = 'RX' . date('Ymd') . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
            if (empty($prescription->qr_token)) {
                $prescription->qr_token = (string) Str::uuid();
            }
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function diagnosisRecord()
    {
        return $this->belongsTo(Diagnosis::class, 'diagnosis_id');
    }

    public function radiologyRequests()
    {
        return $this->hasMany(RadiologyRequest::class);
    }

    public function laboratoryRequests()
    {
        return $this->hasMany(LaboratoryRequest::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
