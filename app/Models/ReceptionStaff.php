<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ReceptionStaff extends Authenticatable
{
    use Notifiable;

    protected $table = 'reception_staff';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class, 'reception_user_id');
    }
}
