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
            'User-Agent' => 'MTG Binder Organizer'
                ." v".config('app.version')
                .config('app.url')
                ." <".config('app.contact').">",
            'Accept' => 'application/json',
        ],
    ],

];
