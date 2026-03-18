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
    Route::get('collection/containers', [\App\Http\Controllers\Collection\ContainerController::class, 'list'])
        ->name('containers');
    Route::get('collection/containers/new', [\App\Http\Controllers\Collection\ContainerController::class, 'create'])
        ->name('container.new');
    Route::post('collection/containers/new', [\App\Http\Controllers\Collection\ContainerController::class, 'store'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('container.store');
    Route::patch('collection/containers/sort', [\App\Http\Controllers\Collection\ContainerController::class, 'reorder'])
        ->name('container.reorder');
    Route::get('collection/containers/{container}/edit', [\App\Http\Controllers\Collection\ContainerController::class, 'edit'])
        ->name('container.edit');
    Route::get('collection/containers/{container}', [\App\Http\Controllers\Collection\ContainerController::class, 'show'])
        ->name('container.show');
    Route::patch('collection/containers/{container}', [\App\Http\Controllers\Collection\ContainerController::class, 'update'])
        ->middleware([HandleControllerPrecognitiveRequest::class])
        ->name('container.update');
    Route::delete('collection/containers/{container}', [\App\Http\Controllers\Collection\ContainerController::class, 'destroy'])
        ->name('container.destroy');

    // add cards
    Route::get('collection/add', [\App\Http\Controllers\Collection\CardsController::class, 'add'])
        ->name('cards.add');
    Route::get('collection/containers/{container}/add', [\App\Http\Controllers\Collection\CardsController::class, 'add'])
        ->name('container.cards.add');


    // decks
    Route::get('/decks', [\App\Http\Controllers\Decks\DecksController::class, 'show'])
        ->name('decks');

});


/******************************************************************************
 * Dev pages (no auth, not linked from anywhere)
 *****************************************************************************/
Route::get('/icons', fn() => Inertia::render('Dev/Icons'))
    ->name('dev.icons');
