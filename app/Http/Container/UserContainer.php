<?php

namespace App\Http\Container;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class UserContainer extends BaseController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        dd('ok');
    }
}
