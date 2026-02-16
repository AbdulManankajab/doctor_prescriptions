<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_number',
        'patient_id',
        'assigned_doctor_id',
        'visit_date',
        'status',
        'reception_user_id',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'assigned_doctor_id');
    }

    public function receptionStaff()
    {
        return $this->belongsTo(ReceptionStaff::class, 'reception_user_id');
    }

    public function prescription()
    {
        return $this->hasOne(Prescription::class);
    }

    public function radiologyRequests()
    {
        return $this->hasMany(RadiologyRequest::class);
    }

    public function laboratoryRequests()
    {
        return $this->hasMany(LaboratoryRequest::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($visit) {
            if (empty($visit->visit_number)) {
                // Same format as RX but using VST prefix or RX as per user's "Visit Number = Prescription Number"
                // Actually if Prescription number = visit number, they should probably share the same sequence or format.
                // The user said: Visit Number = Prescription Number.
                // Let's use RX prefix to be consistent if they are the same.
                $visit->visit_number = 'RX' . date('Ymd') . str_pad(self::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
