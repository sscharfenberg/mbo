<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Laravel\Fortify\Features;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
