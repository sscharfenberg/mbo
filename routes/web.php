<?php

use Illuminate\Support\Facades\Route;

/******************************************************************************
 * Guest pages
 *****************************************************************************/
// Start
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'show'])
    ->name('welcome');
// Language Switcher
Route::get('/lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'update'])
    ->name('locale');
// Privacy
Route::get('/privacy', [\App\Http\Controllers\GuestController::class, 'privacy'])
    ->name('privacy');
// Imprint
Route::get('/imprint', [\App\Http\Controllers\GuestController::class, 'imprint'])
    ->name('imprint');

/******************************************************************************
 * Auth pages, not logged in
 *****************************************************************************/
// register
Route::get('/register', [\App\Http\Controllers\User\AuthController::class, 'registerView'])
    ->name('register');
// login
Route::get('/login', [\App\Http\Controllers\User\AuthController::class, 'loginView'])
    ->name('login');
// email verification (no auth required - uses signed URL)
Route::get('/verify-email/{id}/{hash}', \App\Http\Controllers\User\VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verify-email');
// forget password or username
Route::get('/forgot', [\App\Http\Controllers\User\ForgotController::class, 'show'])
    ->name('forgot');

/******************************************************************************
 * Authed pages
 *****************************************************************************/
Route::middleware(['auth', 'verified'])->group( function() {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'show'])
        ->name('dashboard');
});
