<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Appointment;
use Illuminate\Support\Arr;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;
use App\Models\BloodDonationRequest;
use App\Models\AppointmentSlot;
use App\Models\DoctorSpeciality;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class ServiceController  extends BaseController
{

    public function bloodDonation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_name' => 'required',
            'patient_age' => 'required',
            'blood_group' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 200);
        }
        $data = $request->all();

        try {
            $row = BloodDonationRequest::create($data);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->sendError($msg, 200);
        }

        return $this->sendResponse($row);
    }
}
