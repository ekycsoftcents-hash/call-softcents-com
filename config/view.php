<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Most Laravel views are stored in the resources/views directory. You may
    | list additional paths here if the application uses multiple themes or
    | view namespaces.
    |
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Blade stores compiled templates in this directory. The installer creates
    | it before Composer boots the application on a fresh Docker deployment.
    |
    */

    'compiled' => env('VIEW_COMPILED_PATH', storage_path('framework/views')),

];

