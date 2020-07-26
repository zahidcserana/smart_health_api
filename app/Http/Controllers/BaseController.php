<?php

namespace App\Http\Controllers;

// use App\Models\Order;


use config;
use Tymon\JWTAuth\JWTAuth;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Controller as Controller;
use App\Http\Container\UserContainer;

class BaseController extends Controller
{
    protected $order, $dateData;
    protected $successMsg = 'Data Successfully saved.';
    protected $errorMsg = 'Something went wrong.';
    protected $domain;
    protected $imageDir;
    protected $currentUser;
    protected $appointment;
    protected $jwt;
    protected $userContainer;

    public function __construct(Guard $auth, JWTAuth $jwt)
    {
        $this->jwt = $jwt;
        $this->userContainer = new UserContainer();
        $this->dateData = array(
            'lastDay' => date("Y-m-d", strtotime("-1 day")),
            'lastWeek' => date("Y-m-d", strtotime("-7 day")),
            'lastMonth' => date("Y-m-d", strtotime("-1 month")),
        );
        $this->currentUser = $auth->user();
        $this->appointment = new Appointment();
        $this->domain = env('DOMAIN_NAME');
        $this->imageDir = $this->domain . '/assets/images/';
    }

    public function sendResponse($data = array(), $message = '')
    {
        $response = [
            'status'  => true,
            'data'    => $data,
            'message' => $message
        ];

        return response()->json($response, 200);
    }

    public function sendError($error, $code = 404)
    {
        $response = [
            'status' => false,
            'data'    => [],
            'message' => $error,
        ];

        return response()->json($response, $code);
    }
}
