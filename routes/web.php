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
Route::get('/register', [\App\Http\Controllers\Auth\AuthController::class, 'registerView'])
    ->name('register');
// login
Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'loginView'])
    ->name('login');
// email verification (no auth required - uses signed URL)
Route::get('/verify-email/{id}/{hash}', \App\Http\Controllers\Auth\VerifyEmailController::class)
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verify-email');
// forgot password or username
Route::get('/forgot', [\App\Http\Controllers\Auth\ForgotController::class, 'show'])
    ->name('forgot');
Route::post('/forgot', [\App\Http\Controllers\Auth\ForgotController::class, 'store'])
    ->middleware(\App\Http\Middleware\HandleControllerPrecognitiveRequest::class)
    ->name('forgot.store');

/******************************************************************************
 * Authed pages
 *****************************************************************************/
Route::middleware(['auth', 'verified'])->group( function() {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'show'])
        ->name('dashboard');
});
