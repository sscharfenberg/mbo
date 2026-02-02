<?php

use Illuminate\Support\Facades\Route;

Route::post('/auth/entropy',
    [\App\Http\Controllers\Api\Auth\EntropyController::class, 'calculate']
);
