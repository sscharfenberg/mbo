<?php

use App\Http\Controllers\Collection\CardsController;
use App\Http\Controllers\Collection\CollectionController;
use App\Http\Controllers\Collection\ContainerController;
use App\Http\Controllers\Decks\DecksController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\HandleControllerPrecognitiveRequest;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

/******************************************************************************
 * Guest pages
 *****************************************************************************/
Route::get('/', [WelcomeController::class, 'show'])
    ->name('welcome');
Route::post('/lang/{locale}', [LocaleController::class, 'update'])
    ->name('locale');
Route::get('/privacy', [GuestController::class, 'privacy'])
    ->name('privacy');
Route::get('/imprint', [GuestController::class, 'imprint'])
    ->name('imprint');

/******************************************************************************
 * Authed pages
 *****************************************************************************/
Route::middleware(array_filter(['auth', Features::enabled(Features::emailVerification()) ? 'verified' : null]))->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'show'])
        ->name('dashboard');

    // collection
    Route::get('/collection', [CollectionController::class, 'list'])
        ->name('collection');
    // Containers
    Route::get('collection/containers', [ContainerController::class, 'list'])
        ->name('containers');
    Route::get('collection/containers/new', [ContainerController::class, 'create'])
        ->name('container.new');
    Route::post('collection/containers/new', [ContainerController::class, 'store'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('container.store');
    Route::patch('collection/containers/sort', [ContainerController::class, 'reorder'])
        ->name('container.reorder');
    Route::get('collection/containers/{container}/edit', [ContainerController::class, 'edit'])
        ->name('container.edit');
    Route::get('collection/containers/{container}', [ContainerController::class, 'show'])
        ->name('container.show');
    Route::patch('collection/containers/{container}', [ContainerController::class, 'update'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('container.update');
    Route::delete('collection/containers/{container}', [ContainerController::class, 'destroy'])
        ->name('container.destroy');

    // add cards
    Route::get('collection/add', [CardsController::class, 'add'])
        ->name('cards.add');
    Route::get('collection/containers/{container}/add', [CardsController::class, 'add'])
        ->name('container.cards.add');
    Route::post('collection/add', [CardsController::class, 'store'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('cards.store');
    Route::get('collection/cardstack/{cardStack}/edit', [CardsController::class, 'edit'])
        ->name('cardstack.edit');
    Route::patch('collection/cardstack/{cardStack}', [CardsController::class, 'update'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('cardstack.update');
    Route::delete('collection/cardstack/{cardStack}', [CardsController::class, 'destroy'])
        ->name('cardstack.destroy');

    // decks
    Route::get('/decks', [DecksController::class, 'show'])
        ->name('decks');

});

/******************************************************************************
 * Dev pages (no auth, not linked from anywhere)
 *****************************************************************************/
Route::get('/icons', fn () => Inertia::render('Dev/Icons'))
    ->name('dev.icons');
