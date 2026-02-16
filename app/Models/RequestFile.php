<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestFile extends Model
{
    protected $fillable = [
        'request_id',
        'request_type',
        'file_path',
        'file_name',
        'file_type',
        'uploaded_by_id',
        'uploaded_by_type',
    ];

    public function request()
    {
        return $this->morphTo();
    }

    public function uploadedBy()
    {
        return $this->morphTo();
    }
}
