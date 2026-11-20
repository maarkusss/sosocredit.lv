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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'goodaff' => [
        'data_url' => env('GOODAFF_DATA_URL', 'https://data.goodday.group/v1/'),
        'dev_data_url' => env('DEV_GOODAFF_DATA_URL', ''), // only for dev env
        'postback_url' => env('GOODAFF_POSTBACK_URL', 'https://postback.goodaff.com'),
        'url' => env('GOODAFF_URL', 'https://api.goodaff.com/api/v1/'),
        'api_key' =>  env('GOODAFF_API_KEY', ''),
        'campaign_id' => env('GOODAFF_CAMPAIGN_ID', ''),
        'country' => env('GOODAFF_COUNTRY'),
        'api_user' => env('GOODAFF_API_USER', ''),
        'api_user_token' => env('GOODAFF_API_USER_TOKEN', ''),
        'api_data_key' => env('API_DATA_KEY'),
        'api_data_url' => env('API_DATA_URL'),
        'use_api_data' => env('USE_API_DATA', false),
    ],

    'hoi_fm' => [
        'api_url' => 'https://hoi.fm/api',
        'email_project_token' => env('HOI_EMAIL_PROJECT_TOKEN'),
        'sms_project_token' => env('HOI_SMS_PROJECT_TOKEN'),
        'push_project_token' => env('HOI_PUSH_PROJECT_TOKEN'),
        'user_token' => env('HOI_USER_TOKEN'),
        'sender_domain' => env('SENDER_DOMAIN'),

        'email_project_prefix' => env('HOI_EMAIL_PROJECT_PREFIX'),
        'sms_project_prefix' => env('HOI_SMS_PROJECT_PREFIX'),
    ],

    'whatsapp' => [
        'api_url' => env('WHATSAPP_URL'),
        'api_key' => env('WHATSAPP_AUTH'),
        'phone_id' => env('WA_PHONE_ID'),
    ],

    'rc' => [
        'base_url' => env('RC_BASE_URL', 'https://www.robocalladmin.goodday.group'),
        'user_token' => env('RC_USER_TOKEN'),
        'project_token' => env('RC_PROJECT_TOKEN'),
        'sms_device' => env('RC_SMS_DEVICE'),
        'sms_port' => env('RC_SMS_PORT'),
    ],

    'sms_club' => [
        'token' => env('SMSCLUB_TOKEN'),
        'api_url' => env('SMSCLUB_API_URL', 'https://im.smsclub.mobi/sms/send'),
    ],

    'sms_center' => [
        'api_url' => env('SMSCENTER_API_URL', 'https://smsc.ru/sys/send.php'),
        'login' => env('SMSCENTER_LOGIN', ''),
        'password' => env('SMSCENTER_PASSWORD', ''),
    ],

    'yandex' => [
        'metrika_counter' => env('YANDEX_METRIKA_COUNTER', ''),
        'metrika_token' => env('YANDEX_METRIKA_TOKEN', ''),
        'client_id_type' => env('YANDEX_METRIKA_CLIENT_ID_TYPE', 'YCLID'),
    ],

    'gocardless' => [
        'secret_id' => env('GOCARDLESS_SECRET_ID', ''),
        'secret_key' => env('GOCARDLESS_SECRET_KEY', ''),
    ],

    'webmaster' => [
        'api_url' => env('WEBMASTER_URL', 'https://api.data.goodday.group'),
        'api_key' => env('WEBMASTER_AUTH'),
        'api_token' => env('WEBMASTER_TOKEN'),
    ],

    'google' => [
        'api_key' => env('GOOGLE_API_KEY'),
        'place_id' => env('GOOGLE_PLACE_ID'),
    ],
];
