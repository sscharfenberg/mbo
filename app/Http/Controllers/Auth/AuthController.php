<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class AuthController extends Controller
{
    /**
     * Display the registration page.
     *
     * Renders the Inertia registration view where new users can create an account.
     *
     * @param  Request  $request
     * @return Response
     */
    public function registerView(Request $request): Response
    {
        return Inertia::render('Auth/Register', []);
    }

    /**
     * Display the login page.
     *
     * Renders the Inertia login view, passing Fortify feature flags so the
     * frontend can conditionally show registration and password-reset links.
     *
     * @param  Request  $request
     * @return Response
     */
    public function loginView(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Features::enabled(Features::resetPasswords()),
            'canRegister' => Features::enabled(Features::registration()),
            'status' => $request->session()->get('status'),
        ]);
    }
}
