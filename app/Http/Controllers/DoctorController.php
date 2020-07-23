<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Appointment;
use Illuminate\Support\Arr;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;
use App\Models\DoctorSchedule;
use App\Models\AppointmentSlot;
use App\Models\DoctorSpeciality;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class DoctorController  extends BaseController
{
    public function index()
    {
        $list = DoctorDetail::with('user')->with('speciality')->with('slots')->with('appointments')->get();

        foreach ($list as $row) {
            $row->user->picture = empty($row->user->picture) ? 'https://rumaisahospital.com/wp-content/uploads/2015/08/LLH-Doctors-Male-Avatar-300x300.png' : $this->imageDir . $row->user->picture;
        }

        return $this->sendResponse($list);
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
        $row = DoctorSchedule::create($data);
        $list = $this->_getList($data['doctor_id'], $data, $row);
        return $this->sendResponse($list);
    }

    public function updateSchedule(Request $request, $id)
    {
        $data = $request->only(['day', 'start_time', 'end_time', 'status']);
        $schedule = DoctorSchedule::findOrfail($id);
        if ($schedule) {
            $schedule->update($data);
            AppointmentSlot::where('doctor_id', $schedule->doctor_id)->where('day', $schedule->day)->delete();
            $list = $this->_getList($schedule->doctor_id, $data, $schedule);
            return $this->sendResponse($list);
        }
        return $this->sendError('Not Found!', 200);
    }

    private function _getList($doctorId, $data, $row)
    {
        $list = DoctorSchedule::where('doctor_id', $doctorId)->get();

        $doctorDetails = DoctorDetail::findOrfail($doctorId);
        $doctorDetails->visiting_days = $list->where('status', 1)->pluck('day');
        if ($data['status']) {
            $doctorDetails->visiting_hrs = [$data['start_time'], $data['end_time']];
        }
        $doctorDetails->update();

        $this->makingSlot($row);

        return $list;
    }

    public function doctorScheduleList($doctorId)
    {
        $list = DoctorSchedule::where('doctor_id', $doctorId)->get();

        return $this->sendResponse($list);
    }

    public function makingSlot($row)
    {
        /* Get Doctor Last Appointment Date; if empty take Current Date */
        /* Slot making for scheduled hours by doctor */
        // $doctorSchedules = DoctorSchedule::where('doctor_id', $doctorId)->where('status', 1)->orderBy('doctor_id', 'desc')->get();

        // foreach ($doctorSchedules as $i => $row) {
        $interval = config('settings.appointmentInterval');
        $start = $row->start_time;
        $end = $row->end_time;

        $startTime = \Carbon\Carbon::createFromFormat('H:i', $start);
        $endTime = \Carbon\Carbon::createFromFormat('H:i', $end);

        $difference = $startTime->diffInMinutes($endTime);

        $count = (int) $difference / $interval;
        $input = array(
            'doctor_id' => $row->doctor_id,
            'day' => $row->day,
            'start_time' => $startTime,
            'end_time' => null
        );
        $slot = AppointmentSlot::create($input);

        for ($i = 1; $i < $count; $i++) {
            $slot->end_time = $startTime->addMinutes($interval);
            $slot->update();
            $input = array(
                'doctor_id' => $row->doctor_id,
                'day' => $row->day,
                'start_time' => $startTime,
                'end_time' => null
            );
            $slot = AppointmentSlot::create($input);
        }
        $slot->end_time = $startTime->addMinutes($interval);
        $slot->update();
        // }
    }
}
