<?php

return [

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
        'bloodGroup' => [
            'A+',
            'A-',
            'B+',
            'B-',
            'O+',
            'O-',
            'AB+',
            'AB-'
        ],
        'gender' => [
            'MALE' => 'Male',
            'FEMALE' => 'Female'
        ],
        'userType' => [
            'USER' => 'User',
            'DOCTOR' => 'Doctor'
        ],
    ],


];
