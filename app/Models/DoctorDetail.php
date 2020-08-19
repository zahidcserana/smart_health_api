<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DoctorDetail extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function speciality()
    {
        return $this->belongsTo('App\Models\DoctorSpeciality', 'specialty_id');
    }

    public function slots()
    {
        return $this->hasMany('App\Models\AppointmentSlot', 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany('App\Models\Appointment', 'doctor_id')
            ->select([
                'doctor_id',
                'appoint_date',
                'slot_time'
            ]);
    }
}
