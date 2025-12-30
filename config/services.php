<?php

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

    /*
    |--------------------------------------------------------------------------
    | Default Whatsapp Service Driver
    |--------------------------------------------------------------------------
    |
    | This option determines the default Whatsapp driver that is utilized for
    | whatsapp communication.
    |
    | Supported: "waba", "ultramsg",
    |
    */
    'whatsapp_driver' => env('WHATSAPP_SERVICE_DRIVER', 'ultramsg'),

    /*
    |--------------------------------------------------------------------------
    | WhatsApp Business API
    |--------------------------------------------------------------------------
    */
    'whatsapp' => [
        'api_url' => env('WHATSAPP_BUSINESS_API_URL'),
        'phone_number_id' => env('WHATSAPP_BUSINESS_PHONE_NUMBER_ID'),
        'account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'access_token' => env('WHATSAPP_BUSINESS_ACCESS_TOKEN'),
        'language_code' => env('WHATSAPP_BUSINESS_LANGUAGE_CODE', 'en_US'),
        'verify_token' => env('WHATSAPP_BUSINESS_VERIFY_TOKEN'),
        'app_id' => env('WHATSAPP_BUSINESS_APP_ID'),
        'app_secret' => env('WHATSAPP_BUSINESS_APP_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | UltraMSG API
    |--------------------------------------------------------------------------
    */
    'ultramsg' => [
        'api_url' => env('ULTRAMSG_API_URL', 'https://api.ultramsg.com'),
        'token' => env('ULTRAMSG_TOKEN', ''),
        'instance_id' => env('ULTRAMSG_INSTANCE_ID', ''),
        'priority' => env('ULTRAMSG_PRIORITY', '10'),
    ],

];
