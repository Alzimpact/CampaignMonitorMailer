<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for Campaign Monitor integration.
    |
    */
    'config' => [
        'apiKey' => env('CAMPAIGN_MONITOR_TRANSACTIONAL_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Campaign Monitor Templates
    |--------------------------------------------------------------------------
    |
    | This file contains all Campaign Monitor smart email templates organized
    | by category for easy access and management.
    |
    */
    'templates' => [],

];
