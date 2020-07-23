<?php

namespace App\Http\Controllers;

// use App\Models\Order;


use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use App\Http\Controllers\Controller as Controller;


class BaseController extends Controller
{
    protected $order, $dateData;
    protected $successMsg = 'Data Successfully saved.';
    protected $errorMsg = 'Something went wrong.';
    protected $imageDir = 'http://shapi.local/assets/images/';
    protected $currentUser;

    public function __construct(Guard $auth)
    {
        $this->dateData = array(
            'lastDay' => date("Y-m-d", strtotime("-1 day")),
            'lastWeek' => date("Y-m-d", strtotime("-7 day")),
            'lastMonth' => date("Y-m-d", strtotime("-1 month")),
        );
        $this->currentUser = $auth->user();
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
