<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment driver. use log for testing
    |
    */

    'default' => env('PAYMENT_DRIVER', 'plogiprapay'),

    /*
    |--------------------------------------------------------------------------
    | Payment Drivers
    |--------------------------------------------------------------------------
    */

    'drivers' => [
        'piprapay' => [
            'base_url' => env('PIPRAPAY_BASE_URL'),
            'api_key' => env('PIPRAPAY_API_KEY'),
        ],
    ],

];
