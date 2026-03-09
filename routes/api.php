<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/entropy',
    [\App\Http\Controllers\Auth\EntropyController::class, 'calculate']
);

Route::get('/card-image/search',
    [\App\Http\Controllers\CardImage\CardImageSearchController::class, 'search']
)->middleware('throttle:60,1');

