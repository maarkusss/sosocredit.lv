<?php

return [
    /**
     * Whether the package functionality should be enabled
     */
    'enabled' => env('POSTBACKS_ENABLED', false),

    /**
     * Whether the postback calls should be logged
     */
    'log_postbacks' => env('POSTBACKS_LOG_POSTBACKS', true),

    /**
     * Whether the package should automatically subscribe to the listened events
     */
    'auto_subscribe' => env('POSTBACKS_AUTO_SUBSCRIBE', true),

    /**
     * Whitelist of Placeholders keys that will be accepted by PostbackQueryBuilder
     *
     * @return array
     */
    'placeholders_whitelist' => [
        'application',
        'consumer',
        'config',
    ],
];
