<?php

return [
    'api_url' => env('WHATSAPP_BUSINESS_API_URL', 'https://graph.facebook.com/v22.0'),
    'phone_number_id' => env('WHATSAPP_BUSINESS_PHONE_NUMBER_ID'),
    'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
    'access_token' => env('WHATSAPP_BUSINESS_ACCESS_TOKEN'),
    'verify_token' => env('WHATSAPP_BUSINESS_VERIFY_TOKEN'),
    'language_code' => env('WHATSAPP_BUSINESS_LANGUAGE_CODE', 'en_US'),
    'version' => 'v22.0',

    'webhook' => [
        'url' => env('APP_URL').'/api/whatsapp/webhook',
        'events' => [
            'messages',
            'message_template_status_update',
            'message_template_quality_update',
        ],
    ],

    'message_types' => [
        'text' => 'text',
        'image' => 'image',
        'document' => 'document',
        'audio' => 'audio',
        'video' => 'video',
        'sticker' => 'sticker',
        'location' => 'location',
        'contacts' => 'contacts',
        'interactive' => 'interactive',
        'template' => 'template',
        'reaction' => 'reaction',
    ],
];
