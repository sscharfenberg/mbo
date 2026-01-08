<?php

use Illuminate\Support\Facades\Route;

// StartPage.
Route::get('/', [\App\Http\Controllers\StartController::class, 'show']);
