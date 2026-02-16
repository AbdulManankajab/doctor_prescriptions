<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class RadiologyStaff extends Authenticatable
{
    use Notifiable;

    protected $table = 'radiology_staff';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function completedRequests()
    {
        return $this->hasMany(RadiologyRequest::class, 'completed_by');
    }
}
