<?php

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
        $router->get('delete/{id}', ['uses' => 'AuthController@delete']);

        /* City */
        $router->get('city-list', ['uses' => 'SettingsController@cityList']);
        $router->get('area-list/{cityId}', ['uses' => 'SettingsController@areaList']);

        /* Settings */
        $router->get('user-settings', ['uses' => 'SettingsController@userSettings']);

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

                /* *** doctor-schedule *** */
                $router->get('doctor-slot/{doctorId}', ['uses' => 'AppointmentController@slotList']);
                // $router->get('doctor-making-slot', ['uses' => 'DoctorController@makingSlot']);
                // $router->get('doctor-schedule-slot', ['uses' => 'AppointmentController@makeScheduleSlot']);
                $router->get('doctor-schedule-list/{doctorId}', ['uses' => 'DoctorController@doctorScheduleList']);
                $router->post('doctor-schedule', ['uses' => 'DoctorController@doctorSchedule']);
                $router->put('doctor-schedule/{id}', ['uses' => 'DoctorController@updateSchedule']);
                $router->post('doctor-schedule/request', ['uses' => 'AppointmentController@requestSchedule']);

                /* *** doctor-api *** */
                $router->get('doctors', ['uses' => 'DoctorController@index']);
            }
        );
    }
);


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, X-Auth-Token, Origin, Authorization');
