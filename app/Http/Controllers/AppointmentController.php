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

class AppointmentController  extends BaseController
{
    public function index()
    {
        $list = $this->appointment->with('user')->with('doctor')->latest()->get();
        foreach ($list as $row) {
            $row->user->picture = empty($row->user->picture) ? config('settings.user_pic') : $this->imageDir . $row->user->picture;
        }

        return $this->sendResponse($list);
    }

    public function slotList($doctorId)
    {
        $collection = AppointmentSlot::select('doctor_id', 'day', 'start_time', 'created_at')->where('doctor_id', $doctorId)->get();
        $list = $collection->groupBy('day');
        $data = [];
        foreach ($list as $key => $row) {
            $data[config('settings.weekdays')[$key]] = $row;
        }
        return $this->sendResponse($data);
    }

    public function requestSchedule(Request $request)
    {
        $formData = $request->all();
        $formData['day'] = (int) $formData['day'];
        $formData['patient_id'] = $this->currentUser->id;
        $exist = Appointment::where('doctor_id', $formData['doctor_id'])->where('appoint_date', $formData['appoint_date'])->where('slot_time', $formData['slot_time'])->first();
        if ($exist) {
            return $this->sendError('Already booked!', 200);
        }
        try {
            Appointment::create($formData);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->sendError($msg, 200);
        }
        $doctor = DoctorDetail::with('appointments')->findOrFail($formData['doctor_id']);

        return $this->sendResponse($doctor);
    }

    /*@unused*/
    public function makeScheduleSlot()
    {
        /* Get Doctor Last Appointment Date; if empty take Current Date */
        $doctorId = 1;
        $lastAppointment = AppointmentSlot::where('doctor_id', $doctorId)->orderBy('start_time', 'desc')->first();

        $lastAppointmentDate = \Carbon\Carbon::now();
        if (!empty($lastAppointment)) {
            $lastAppointmentDate = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lastAppointment->start_time);
            $dayDiff = $lastAppointmentDate->diffInDays(\Carbon\Carbon::now());
            if ($dayDiff > 30) {
                return $this->sendError('Remaining ' . $dayDiff . ' days schedules.', 200);
            }
        }
        $numberOfScheduleDays = 30;
        $doctorDetails = DoctorDetail::findOrfail($doctorId);

        for ($t = 0; $t < $numberOfScheduleDays; $t++) {
            /* Start making Schedule from next day to after several days */
            $lastAppointmentDate->addDays(1);

            $days = json_decode($doctorDetails->visiting_days, true);
            /* Slot making only scheduled days by doctor */
            if (!in_array($lastAppointmentDate->dayOfWeek, $days)) {
                continue;
            }

            /* get date from datetime */
            $dateTime = new \DateTime($lastAppointmentDate);
            $date = $dateTime->format('Y-m-d');

            /* Slot making for scheduled hours by doctor */
            $doctorSchedules = DoctorSchedule::where('doctor_id', $doctorId)->where('status', 1)->orderBy('doctor_id', 'desc')->get();
            foreach ($doctorSchedules as $i => $row) {
                $interval = config('settings.appointmentInterval');
                $start = $date . ' ' . $row->start_time;
                $end = $date . ' ' . $row->end_time;

                $startTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $start);
                $endTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $end);

                $difference = $startTime->diffInMinutes($endTime);

                $count = (int) $difference / $interval;
                $input = array(
                    'doctor_id' => $row->doctor_id,
                    'start_time' => $startTime,
                    'end_time' => null
                );
                $slot = AppointmentSlot::create($input);

                for ($i = 1; $i < $count; $i++) {
                    $slot->end_time = $startTime->addMinutes($interval);
                    $slot->update();
                    $input = array(
                        'doctor_id' => $row->doctor_id,
                        'start_time' => $startTime,
                        'end_time' => null
                    );
                    $slot = AppointmentSlot::create($input);
                }
                $slot->end_time = $startTime->addMinutes($interval);
                $slot->update();
            }
        }
    }
}
