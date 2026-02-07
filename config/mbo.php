<?php

return [

    /*
    |--------------------------------------------------------------------------
    | app settings
    |--------------------------------------------------------------------------
    |
    | basic settings for this app
    |
    */
    'app' => [
        'supportedLocales' => [
            'de',
            'en'
        ]
    ],

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
