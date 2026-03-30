<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    /**
     * Display the registration page.
     */
    public function registerView(Request $request): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Display the login page.
     *
     * Passes the session status (e.g. "password reset successful") to the
     * Inertia view so it can be shown as a flash message.
     */
    public function loginView(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => $request->session()->get('status'),
        ]);
    }
}
