<?php

return [
    /**
     * Enable Google Recaptcha in forms
     */
    'enabled' => env('RECAPTCHA_ENABLED', false),

    /**
     * API Key for Google Recaptcha
     */
    'api_key' => env('RECAPTCHA_API_KEY', ''),

    /**
     * Secret Key for Google Recaptcha
     */
    'secret_key' => env('RECAPTCHA_SECRET_KEY', ''),

    /**
     * API Domain for Google Recaptcha
     */
    'api_domain' => 'https://www.google.com/recaptcha/api/siteverify',
];
