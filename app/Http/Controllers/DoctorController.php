<?php

namespace App\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Models\DoctorSchedule;
use App\Models\AppointmentSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

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
        $validator = Validator::make($request->all(), [
            'day' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 200);
        }
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

    public function makeScheduleSlot()
    {
        $lastAppointmentDate = AppointmentSlot::where('doctor_id', 1)->orderBy('start_time', 'desc')->first();
        $s = $lastAppointmentDate->start_time;
        $dt = new \DateTime($s);

        $date = $dt->format('Y-m-d');

        $doctorSchedules = DoctorSchedule::where('status', 1)->orderBy('doctor_id', 'desc')->get();
        foreach ($doctorSchedules as $i => $row) {
            $interval = config('settings.appointmentInterval');
            $start = $row->start_time;
            $end = $row->end_time;

            $startTime = \Carbon\Carbon::createFromFormat('H:s', $start);
            $endTime = \Carbon\Carbon::createFromFormat('H:s', $end);


            $difference = $startTime->diffInMinutes($endTime);

            $count = (int) $difference / $interval;
            $nextTime = $startTime;

            for ($i = 0; $i < $count; $i++) {
                $appointmentStart = $nextTime;
                dd($appointmentStart);

                $nextTime = $nextTime->addMinutes($interval);
                $input = array(
                    'doctor_id' => $row->doctor_id,
                    'start_time' => $appointmentStart,
                    'end_time' => $nextTime
                );
                $slot = AppointmentSlot::create($input);
                // echo $nextTime;
                // echo '<br>';
            }
        }
        // dd($result);
    }
}
