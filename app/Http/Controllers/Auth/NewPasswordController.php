<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\PasswordValidationRules;

class NewPasswordController extends Controller
{

    use PasswordValidationRules;

    /**
     * @function show "reset password" page
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->get('email'),
            'token' => $request->get('token'),
        ]);
    }

    /**
     * @function store new password
     * @param Request $request
     * @return Response
     */
    public function store(Request $request): Response
    {
        precognitive(function () use ($request) {
            $request->validate([
                'token' => 'required',
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                    'exists:users,email'
                ],
                'password' => $this->passwordRules(),
                'password_confirmation' => ['same:password'],
            ]);
        });

        // store new password

    }

}
