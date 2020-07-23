<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];

    public function doctor()
    {
        return $this->belongsTo('App\Models\DoctorDetail', 'doctor_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'patient_id');
    }
}
