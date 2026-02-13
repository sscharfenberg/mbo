<?php

use Illuminate\Support\Facades\Route;

/**
 * Guest pages
 */
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

/**
 * Auth pages, not logged in
 */
// register
Route::get('/register', [\App\Http\Controllers\User\AuthController::class, 'registerView'])
    ->name('register');
// login
Route::get('/login', [\App\Http\Controllers\User\AuthController::class, 'loginView'])
    ->name('login');

/**
 * Authed pages
 */
Route::middleware(['auth'])->group( function() {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'show'])
        ->name('dashboard');
});
