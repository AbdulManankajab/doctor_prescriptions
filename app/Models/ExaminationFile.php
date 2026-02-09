<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ExaminationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'examination_id',
        'file_path',
        'file_type',
    ];

    public function examination()
    {
        return $this->belongsTo(Examination::class);
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->file_path);
    }
}
