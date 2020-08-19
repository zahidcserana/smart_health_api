<?php

namespace App\Http\Controllers;

use Validator;
use App\Models\Appointment;
use Illuminate\Support\Arr;
use App\Models\DoctorDetail;
use Illuminate\Http\Request;
use App\Models\BloodDonation;
use App\Models\AppointmentSlot;
use App\Models\DoctorSpeciality;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;
use App\Models\AmbulanceBooking;
use App\Models\AmbulanceVendor;

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
        $data['user_id'] = $this->user->id;

        try {
            $row = BloodDonation::create($data);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->sendError($msg, 200);
        }

        return $this->sendResponse($row);
    }

    public function bloodDonationList(Request $request)
    {
        $list = BloodDonation::with('user')->latest()->get();
        foreach ($list as $row) {
            $row->user->avatar = empty($row->user->picture) ? config('settings.user_pic') : $this->imageDir . $row->user->picture;
        }
        return $this->sendResponse($list);
    }

    public function storeAmbulance(Request $request) {
        $formData = $request->all();

        AmbulanceVendor::create($formData);

        return $this->sendResponse([], $this->successMsg);
    }

    public function getAmbulanceList() {
        $list = AmbulanceVendor::get();
        foreach ($list as $row) {
            $row->vendor_image = empty($row->vendor_image) ? $this->imageDir . config('settings.default_image.ambulance') : $this->imageDir . $row->vendor_image;
        }

        return $this->sendResponse($list);
    }

    public function storeAmbulanceBooking(Request $request) {
        $validator = Validator::make($request->all(), [
            'ambulance_vendor_id' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first(), 200);
        }
        $data = $request->all();
        $data['user_id'] = $this->user->id;

        try {
            $row = AmbulanceBooking::create($data);
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            return $this->sendError($msg, 200);
        }

        return $this->sendResponse($row);
    }

    public function ambulanceBookingList() {
        $list = AmbulanceBooking::with('user')->with('ambulance_vendor')->get();

        foreach ($list as $row) {
            $row->ambulance_image = empty($row->ambulance_vendor->vendor_image) ? $this->imageDir . config('settings.default_image.ambulance') : $this->imageDir . $row->ambulance_vendor->vendor_image;
        }

        return $this->sendResponse($list);
    }
}
