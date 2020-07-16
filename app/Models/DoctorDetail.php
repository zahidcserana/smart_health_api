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
}
