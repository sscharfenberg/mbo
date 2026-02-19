<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/entropy',
    [\App\Http\Controllers\Auth\EntropyController::class, 'calculate']
);
