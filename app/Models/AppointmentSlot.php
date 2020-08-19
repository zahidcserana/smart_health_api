<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentSlot extends Model
{
    // use SoftDeletes;
    protected $guarded = [];

    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function doctor()
    {
        return $this->belongsTo('App\Models\DoctorDetail', 'doctor_id');
    }
}
