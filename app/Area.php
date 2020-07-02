<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = [
        'name',
        'city_id'
    ];

    public $timestamps = true;

    public function city()
    {
        return $this->belongsTo('App\City');
    }
}
