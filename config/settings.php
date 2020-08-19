<?php

return [

    'appointmentInterval' => 20,

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
    ],
    'domain_name' => 'http://shapi.local/',

    'default_image' => [
        'ambulance' => 'default/ambulance.png'
    ],

    'user_pic' => 'https://avatars0.githubusercontent.com/u/1472352?s=460&v=4',
    'doctor_pic' => 'https://rumaisahospital.com/wp-content/uploads/2015/08/LLH-Doctors-Male-Avatar-300x300.png',
    'guards' => [
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users'
        ],
    ],

    'userSettings' => [
        'bloodGroups' => [
            'A+',
            'A-',
            'B+',
            'B-',
            'O+',
            'O-',
            'AB+',
            'AB-'
        ],
        'genders' => [
            'MALE' => 'Male',
            'FEMALE' => 'Female'
        ],
        'userTypes' => [
            'USER' => 'User',
            'DOCTOR' => 'Doctor'
        ],
    ],

    'weekdays' => [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
    ]

];
