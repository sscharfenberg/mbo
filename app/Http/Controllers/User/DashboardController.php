<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class DashboardController extends Controller
{
    /**
     * Display the user dashboard.
     *
     * Entry point for authenticated users after login. Renders the main
     * dashboard view with the current request context.
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Dashboard/Dashboard', [
            'request' => $request,
            'twoFactorEnabled' => $request->user()->hasEnabledTwoFactorAuthentication(),
            'requiresConfirmation' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirm'),
            'requiresPasswordConfirmation' => Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
        ]);
    }
}
