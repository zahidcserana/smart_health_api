<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class SettingsController  extends BaseController
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

    public function userSettings()
    {
        $data = config('settings.userSettings');
        $data['doctorSpecialties'] = DB::table('doctor_specialties')->pluck('id', 'title');

        return $this->sendResponse($data);
    }
}
