<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use \App\Http\Middleware\HandleControllerPrecognitiveRequest;

/******************************************************************************
 * Guest pages
 *****************************************************************************/
// Start
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'show'])
    ->name('welcome');
// Language Switcher (accessible to both guests and authenticated users)
Route::post('/lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'update'])
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
Route::middleware(['guest:'.config('fortify.guard')])->group( function() {
    Route::get('/login', [\App\Http\Controllers\Auth\AuthController::class, 'loginView'])
        ->name('login');

    if (Features::enabled(Features::registration())) {
        Route::get('/register', [\App\Http\Controllers\Auth\AuthController::class, 'registerView'])
            ->name('register');
    }

    if (Features::enabled(Features::resetPasswords())) {
        Route::get('/forgot', [\App\Http\Controllers\Auth\ForgotController::class, 'show'])
            ->name('forgot');
        Route::post('/forgot', [\App\Http\Controllers\Auth\ForgotController::class, 'store'])
            ->middleware(['throttle:6,1', HandleControllerPrecognitiveRequest::class])
            ->name('forgot.store');
        Route::get('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'show'])
            ->name('password.reset');
        Route::post('reset-password', [\App\Http\Controllers\Auth\NewPasswordController::class, 'store'])
            ->middleware([HandleControllerPrecognitiveRequest::class])
            ->name('password.reset');
    }

    if (Features::enabled(Features::emailVerification())) {
        Route::get('/verify-email/{id}/{hash}', \App\Http\Controllers\Auth\VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verify-email');
        Route::get('resend-verification', [\App\Http\Controllers\Auth\ResendVerificationController::class, 'show'])
            ->name('verification.resend');
        Route::post('resend-verification', [\App\Http\Controllers\Auth\ResendVerificationController::class, 'store'])
            ->middleware(['throttle:6,1', HandleControllerPrecognitiveRequest::class])
            ->name('verification.resend');
    }
});

/******************************************************************************
 * Authed pages
 *****************************************************************************/
Route::middleware(array_filter(['auth', Features::enabled(Features::emailVerification()) ? 'verified' : null]))->group( function() {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'show'])
        ->name('dashboard');

    // Password Confirmation (sets session for Fortify's password.confirm middleware)
    Route::post('/confirm-password', [\App\Http\Controllers\User\ConfirmPasswordController::class, 'store'])
        ->middleware(['throttle:6,1'])
        ->name('password.confirm');

    // Collection
    Route::get('/collection', [\App\Http\Controllers\Mbo\CollectionController::class, 'show'])
        ->name('collection');

    // Decks
    Route::get('/decks', [\App\Http\Controllers\Mbo\DecksController::class, 'show'])
        ->name('decks');
});

/******************************************************************************
 * Dev pages (no auth, not linked from anywhere)
 *****************************************************************************/
Route::get('/icons', fn() => Inertia::render('Dev/Icons'))
    ->name('dev.icons');
