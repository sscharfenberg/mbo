<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\HandleInertiaRequests;
use Laravel\Fortify\Fortify;

// Load Fortify routes with app routes so they exist when route:cache is used on the server.
Fortify::ignoreRoutes();

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::group([
                'domain' => config('fortify.domain'),
                'prefix' => config('fortify.prefix'),
            ], base_path('vendor/laravel/fortify/routes/routes.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->web(append: [
            HandleInertiaRequests::class, // 2. Append it here
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
