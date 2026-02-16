<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class LaboratoryStaff extends Authenticatable
{
    use Notifiable;

    protected $table = 'laboratory_staff';

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
        return $this->hasMany(LaboratoryRequest::class, 'completed_by');
    }
}
