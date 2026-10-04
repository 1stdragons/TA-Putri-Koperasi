<?php

return [

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'akuns'),
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'akuns',
        ],
    ],

    'providers' => [
        'akuns' => [
            'driver' => 'eloquent',
            'model' => App\Models\Akun::class,
        ],
    ],

    'passwords' => [
        'akuns' => [
            'provider' => 'akuns',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];