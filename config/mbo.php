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
    'scryfall' => [
        'header' => [
            'User-Agent' => env('APP_NAME', 'MTG Binder Organizer')
                ." ".config('app.version')
                ." <".config('app.contact').">",
            'Accept' => 'application/json',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | database settings
    |--------------------------------------------------------------------------
    |
    | database constraints
    |
    */
    'db' => [
        'user' => [
            'name' => [
                'min' => 8,
                'max' => 80
            ]
        ],
        'binder' => [
            'name' => [
                'max' => 128
            ],
            'type' => [
                'max' => 64
            ],
            'custom_type' => [
                'max' => 64
            ]
        ]
    ]

];
