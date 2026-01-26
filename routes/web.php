<?php

use Illuminate\Support\Facades\Route;

/**
 * Guest pages
 */
// Start
Route::get('/', [\App\Http\Controllers\StartController::class, 'show']);
// Privacy
Route::get('/privacy', [\App\Http\Controllers\GuestController::class, 'privacy']);
// Imprint
Route::get('/imprint', [\App\Http\Controllers\GuestController::class, 'imprint']);

/**
 * Authentification
 */
// Start
Route::get('/auth/register', [\App\Http\Controllers\Auth\RegistrationController::class, 'show']);
