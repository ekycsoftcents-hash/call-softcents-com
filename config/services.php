<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'piprapay' => [
        'base_url' => env('PIPRAPAY_BASE_URL', 'https://pay.frolax.agency/api'),
        'api_key' => env('PIPRAPAY_API_KEY'),
    ],

    'azure' => [
        'tts' => [
            'key' => env('AZURE_TTS_KEY'),
            'region' => env('AZURE_TTS_REGION', 'southeastasia'),
        ],
    ],

    'frolax' => [
        'tts' => [
            'base_url' => env('FROLAX_TTS_BASE_URL', 'https://tts.frolax.agency'),
            'api_key' => env('FROLAX_TTS_API_KEY'),
        ],
    ],

    'fusionpbx' => [
        'voice_extension' => env('FUSIONPBX_VOICE_EXTENSION', '5001'),
        'voice_context' => env('FUSIONPBX_VOICE_CONTEXT', 'default'),
        'ivr_extension' => env('FUSIONPBX_IVR_EXTENSION', '5000'),
        'ivr_context' => env('FUSIONPBX_IVR_CONTEXT', 'default'),
    ],
];
