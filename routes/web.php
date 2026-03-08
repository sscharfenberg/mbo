<?php

use App\Http\Middleware\HandleControllerPrecognitiveRequest;
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
    Route::get('/collection', [\App\Http\Controllers\Collection\CollectionController::class, 'list'])
        ->name('collection');
    // Containers
    Route::get('collection/containers', [\App\Http\Controllers\Collection\ContainerController::class, 'show'])
        ->name('containers');
    Route::get('collection/containers/new', [\App\Http\Controllers\Collection\ContainerController::class, 'create'])
        ->name('container.new');
    Route::post('collection/containers/new', [\App\Http\Controllers\Collection\ContainerController::class, 'store'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('container.store');

    // decks
    Route::get('/decks', [\App\Http\Controllers\Decks\DecksController::class, 'show'])
        ->name('decks');
});

/******************************************************************************
 * Dev pages (no auth, not linked from anywhere)
 *****************************************************************************/
Route::get('/icons', fn() => Inertia::render('Dev/Icons'))
    ->name('dev.icons');
