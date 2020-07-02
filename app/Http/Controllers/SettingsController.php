<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;

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

        return $this->sendResponse($data);
    }
}
