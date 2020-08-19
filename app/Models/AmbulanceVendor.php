<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceVendor extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];


    public function ambulanceBookings()
    {
        return $this->hasMany('App\Models\AmbulanceBooking');
    }
}
