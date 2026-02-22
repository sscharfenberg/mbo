<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function registerView(Request $request): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function loginView(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'status' => $request->session()->get('status'),
        ]);
    }
}
