<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PharmacyUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'facility_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
}
