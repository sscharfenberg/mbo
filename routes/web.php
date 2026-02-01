<?php

use Illuminate\Support\Facades\Route;

/**
 * Guest pages
 */
// Start
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'show']);
// Privacy
Route::get('/privacy', [\App\Http\Controllers\GuestController::class, 'privacy']);
// Imprint
Route::get('/imprint', [\App\Http\Controllers\GuestController::class, 'imprint']);
