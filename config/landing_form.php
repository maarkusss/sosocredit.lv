<?php

/**
 * Configuration file for (main and only) Landing form on the homepage
 */

return [
    /**
     * Whether the email input should be shown in Landing form
     */
    'require_email' => env('LANDING_FORM_REQUIRE_EMAIL', true),

    /**
     * Whether the phone input should be shown in Landing form
     */
    'require_phone' => env('LANDING_FORM_REQUIRE_PHONE', true),

    /**
     * Whether the optional subscribe to offers checkbox should be shown
     */
    'show_optional_subscribe' =>  env('LANDING_FORM_SHOW_OPTIONAL_SUBSCRIBE', true),

    /**
     * Will apply phone number mask using IMask JS Library for all elements having "applyPhoneMask" CSS Class
     */
    'apply_phone_mask' => env('LANDING_FORM_APPLY_PHONE_MASK', true),

    /**
     * Text to appear in phone input fields as placeholder after phone extension
     * IMPORTANT! Pay attention to the number of symbols here - has to match the amount of symbols after extension\
     * , for instance
     * - KZ should have 10 symbols
     * - LV should have 8 symbols
     * - PL (Poland) should have 9 symbols
     * - UK (Ukraine) should have 7 symbols
     * - VI (Vietnam) should have 10 symbols
     */
    'phone_placeholder' => env('LANDING_FORM_PHONE_PLACEHOLDER', 'XXXXXXXX'),
];
