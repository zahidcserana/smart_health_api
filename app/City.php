<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name'
    ];

    public $timestamps = true;

    public function areas()
    {
        return $this->hasMany('App\Area');
    }
}
