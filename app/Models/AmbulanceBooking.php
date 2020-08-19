<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AmbulanceBooking extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];

    public function ambulance_vendor()
    {
        return $this->belongsTo('App\Models\AmbulanceVendor');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
