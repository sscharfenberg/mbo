<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\PasswordValidationRules;

class UpdatePasswordController extends Controller
{

    use PasswordValidationRules;

    /**
     * Update the authenticated user's password.
     *
     * Validates the current password, the new password (including entropy),
     * and confirmation via precognitive validation. On success the password
     * is hashed and persisted, and the user is redirected back with a
     * success flash.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'current_password' => ['required', 'string', 'current_password:web'],
                'password' => $this->passwordRules(),
                'password_confirmation' => ['same:password'],
            ]);
        });

        $request->user()->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        $request->session()->flash('message', __('passwords.updated'));
        $request->session()->flash('type', 'success');

        return back();
    }

}
