<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DoctorController  extends BaseController
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

    public function doctorSchedule(Request $request)
    {
        $data = $request->all();
        $schedule = DoctorSchedule::where('doctor_id', $data['doctor_id'])->where('day', $data['day'])->first();
        if ($schedule) {
            return $this->sendError('Already added!', 200);
        }

        DoctorSchedule::create($data);

        $list = DoctorSchedule::where('doctor_id', $data['doctor_id'])->get();

        return $this->sendResponse($list);
    }

    public function updateSchedule(Request $request, $id)
    {
        $data = $request->only(['start_time', 'end_time', 'status']);

        $schedule = DoctorSchedule::findOrfail($id);


        if ($schedule) {

            $schedule->update($data);

            $list = DoctorSchedule::where('doctor_id', $schedule->doctor_id)->get();

            return $this->sendResponse($list);
        }
        return $this->sendError('Not Found!', 200);
    }

    public function doctorScheduleList($doctorId)
    {
        $list = DoctorSchedule::where('doctor_id', $doctorId)->get();

        return $this->sendResponse($list);
    }
}
