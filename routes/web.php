<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/******************************************************************************
 * Guest pages
 *****************************************************************************/
Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'show'])
    ->name('welcome');
Route::post('/lang/{locale}', [\App\Http\Controllers\LocaleController::class, 'update'])
    ->name('locale');
Route::get('/privacy', [\App\Http\Controllers\GuestController::class, 'privacy'])
    ->name('privacy');
Route::get('/imprint', [\App\Http\Controllers\GuestController::class, 'imprint'])
    ->name('imprint');

/******************************************************************************
 * Authed pages
 *****************************************************************************/
Route::middleware(array_filter(['auth', Features::enabled(Features::emailVerification()) ? 'verified' : null]))->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\DashboardController::class, 'show'])
        ->name('dashboard');

    // collection
    Route::get('/collection', [\App\Http\Controllers\Collection\CollectionController::class, 'show'])
        ->name('collection');
    Route::get('collection/container/new', [\App\Http\Controllers\Collection\ContainerController::class, 'showNew'])
        ->name('container.new');

    // decks
    Route::get('/decks', [\App\Http\Controllers\Decks\DecksController::class, 'show'])
        ->name('decks');
});

/******************************************************************************
 * Dev pages (no auth, not linked from anywhere)
 *****************************************************************************/
Route::get('/icons', fn() => Inertia::render('Dev/Icons'))
    ->name('dev.icons');
