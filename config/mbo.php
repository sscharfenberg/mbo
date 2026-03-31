<?php

return [

    /*
    |--------------------------------------------------------------------------
    | scryfall settings
    |--------------------------------------------------------------------------
    |
    | basic settings for scryfall imports.
    |
    */
    /*
    |--------------------------------------------------------------------------
    | CSV import settings
    |--------------------------------------------------------------------------
    */
    'csv_upload_max_bytes' => 2 * 1024 * 1024, // 2 MB
    'csv_upload_allowed_types' => ['.csv'],

    /*
    |--------------------------------------------------------------------------
    | scryfall settings
    |--------------------------------------------------------------------------
    |
    | basic settings for scryfall imports.
    |
    */
    'scryfall' => [
        'header' => [
            'User-Agent' => 'MTG Binder Organizer'
                .' v'.config('app.version')
                .config('app.url')
                .' <'.config('app.contact').'>',
            'Accept' => 'application/json',
        ],
    ],

];
