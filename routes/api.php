<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/entropy',
    [\App\Http\Controllers\Auth\EntropyController::class, 'calculate']
);

Route::get('/art-crop',
    [\App\Http\Controllers\Api\DefaultCardsController::class, 'artCropSearch']
)->middleware('throttle:60,1');

Route::get('/card-image',
    [\App\Http\Controllers\Api\DefaultCardsController::class, 'searchCardImage']
)->middleware('throttle:60,1')->name('cards.search');

