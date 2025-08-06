<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Track Authenticated Users Only
    |--------------------------------------------------------------------------
    |
    | When enabled, only hits from authenticated users will be tracked.
    | Anonymous visits will be ignored.
    |
    */
    'authenticated_only' => false,

    /*
    |--------------------------------------------------------------------------
    | Bot User Agents
    |--------------------------------------------------------------------------
    |
    | List of user agent patterns to ignore when tracking hits.
    | These patterns are checked using case-insensitive string matching.
    |
    */
    'bot_user_agents' => [
        'bingbot',
        'bot',
        'crawler',
        'facebookexternalhit',
        'googlebot',
        'linkedinbot',
        'scraper',
        'slackbot',
        'spider',
        'telegrambot',
        'twitterbot',
        'whatsapp',
    ],

    /*
    |--------------------------------------------------------------------------
    | Cooldown Period
    |--------------------------------------------------------------------------
    |
    | Minimum time (in minutes) between hits from the same IP address
    | for the same model. Set to 0 to disable cooldown.
    |
    */
    'cooldown_minutes' => 5,

    /*
    |--------------------------------------------------------------------------
    | Ignore Bots
    |--------------------------------------------------------------------------
    |
    | When enabled, requests from common bots and crawlers will be ignored.
    | This helps keep your hit counts more accurate for real users.
    |
    */
    'ignore_bots' => true,
];
