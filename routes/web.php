<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/


$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->group(
    ['prefix' => 'api'],
    function () use ($router) {

        $router->post('auth/login', ['uses' => 'AuthController@postLogin']);
        $router->post('auth/register', ['uses' => 'AuthController@register']);
        $router->post('auth/login/mobile', ['uses' => 'AuthController@mobileLogin']);
        $router->post('auth/login/mobile/otp', ['uses' => 'AuthController@mobileLoginOtp']);
        $router->post('file/upload', ['uses' => 'AuthController@fileUpload']);

        $router->group(
            ['middleware' => 'auth:api'],
            function () use ($router) {
                $router->get('/test', function () {
                    return response()->json([
                        'message' => 'Hello World!',
                    ]);
                });
                $router->post('logout', ['uses' => 'AuthController@logout']);
                $router->post('refresh', ['uses' => 'AuthController@refresh']);
                $router->post('me', ['uses' => 'AuthController@me']);
                $router->put('users/{id}', ['uses' => 'AuthController@update']);

                /* City */
                $router->get('city-list', ['uses' => 'AuthController@cityList']);
                $router->get('area-list/{cityId}', ['uses' => 'AuthController@areaList']);

                /* Settings */
                $router->get('user-settings', ['uses' => 'SettingsController@userSettings']);

                /* *** doctor-schedule *** */
                $router->get('doctor-schedule-slot', ['uses' => 'DoctorController@makeScheduleSlot']);
                $router->get('doctor-schedule-list/{doctorId}', ['uses' => 'DoctorController@doctorScheduleList']);
                $router->post('doctor-schedule', ['uses' => 'DoctorController@doctorSchedule']);
                $router->put('doctor-schedule/{id}', ['uses' => 'DoctorController@updateSchedule']);
            }
        );
    }
);


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
