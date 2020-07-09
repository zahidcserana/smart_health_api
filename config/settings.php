<?php

return [

    'appointmentInterval' => 20,

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
    ],

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


];
