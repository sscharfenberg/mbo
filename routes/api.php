<?php

use App\Http\Controllers\Api\CommanderController;
use App\Http\Controllers\Api\DefaultCardsController;
use App\Http\Controllers\Auth\EntropyController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/entropy',
    [EntropyController::class, 'calculate']
);

Route::get('/art-crop',
    [DefaultCardsController::class, 'artCropSearch']
)->middleware('throttle:60,1');

Route::get('/card-image',
    [DefaultCardsController::class, 'searchCardImage']
)->middleware('throttle:60,1')->name('cards.search');

Route::get('/commander',
    [CommanderController::class, 'search']
)->middleware('throttle:60,1');

Route::get('/oathbreaker',
    [CommanderController::class, 'searchOathbreaker']
)->middleware('throttle:60,1');
