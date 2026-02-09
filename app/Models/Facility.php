<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'logo_path',
        'address',
        'phone',
        'email',
        'license_number',
        'province',
        'district',
        'status',
    ];

    /**
     * Get the doctors associated with the facility.
     */
    public function doctors()
    {
        return $this->hasMany(Doctor::class);
    }
}
