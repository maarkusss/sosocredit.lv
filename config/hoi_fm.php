<?php

return [
    /**
     * Should Consumer phone numbers be exported to HoiFm
     *
     * 'all'        => Export every phone number disregarding the verification status
     * 'subscribed' => Export only phone numbers of consumers who explicitly subscribed to offers. Can be unverified!
     * 'verified'   => Export only phone numbers that have been verified
     * null         => Do not export phone numbers
     */
    'export_phone_numbers' => 'verified',

    /**
     * Should Consumer email addresses be exported to HoiFm
     *
     * 'all'        => Export every email disregarding the verification status
     * 'subscribed' => Export only emails of consumers who explicitly subscribed to offers. Can be unverified!
     * 'verified'   => Export only emails that have been verified
     * null         => Do not export emails
     */
    'export_emails' => 'verified',

    /**
     * Should Consumers be exported to HoiFm Push project
     *
     * true        => Export all consumers to PUSH project on each POST request
     * false       => Do not export all consumers to PUSH project
     */
    'export_push_consumers' => true,
];
