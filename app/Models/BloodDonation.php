<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BloodDonation extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
