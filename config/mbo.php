<?php

return [

    /*
    |--------------------------------------------------------------------------
    | scryfall settings
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */
    'scryfall' => [
        'header' => [
            'User-Agent' => env('APP_NAME', 'MTG Binder Organizer')
                ." ".config('app.version')
                ." <".config('app.contact').">",
            'Accept' => 'application/json',
        ],
        'set_types' => [
            'core',
            'expansion',
            'masters',
            'eternal',
            'alchemy',
            'masterpiece',
            'arsenal',
            'from_the_vault',
            'spellbook',
            'premium_deck',
            'duel_deck',
            'draft_innovation',
            'treasure_chest',
            'commander',
            'planechase',
            'archenemy',
            'vanguard',
            'funny',
            'starter',
            'box',
            'promo',
            'token',
            'memorabilia',
            'minigame'
        ],
        'card_layout' => [
            'normal',
            'split',
            'flip',
            'transform',
            'modal_dfc',
            'meld',
            'leveler',
            'class',
            'case',
            'saga',
            'adventure',
            'mutate',
            'prototype',
            'battle',
            'planar',
            'scheme',
            'vanguard',
            'token',
            'double_faced_token',
            'emblem',
            'augment',
            'host',
            'art_series',
            'reversible_card'
        ],
        'lang' => [
            'en',
            'es',
            'fr',
            'de',
            'it',
            'pt',
            'ja',
            'ko',
            'ru',
            'zhs',
            'zht',
            'he',
            'la',
            'grc',
            'ar',
            'sa',
            'ph',
            'qya'
        ],
        'rarity' => [
            'common',
            'uncommon',
            'rare',
            'special',
            'mythic',
            'bonus'
        ]
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
        ]
    ]

];
