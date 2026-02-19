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
     * @function show "Register" page
     * @param Request $request
     * @return Response
     */
    public function registerView(Request $request): Response
    {
        return Inertia::render('Auth/Register', []);
    }

    /**
     * @function show "Login" page
     * @param Request $request
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
