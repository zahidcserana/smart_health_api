<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSpeciality extends Model
{
    protected $table = 'doctor_specialties';

    protected $guarded = [];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];

    public function doctor()
    {
        return $this->hasMany('App\Models\DoctorDetail', 'specialty_id');
    }
}
