<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use App\Traits\PasswordValidationRules;

class NewPasswordController extends Controller
{

    use PasswordValidationRules;

    /**
     * @function show "reset password" page
     */
    public function show(Request $request): Response|RedirectResponse
    {
        $user = User::where('email', $request->get('email'))->first();

        if (!$user || !Password::broker(config('fortify.passwords'))->tokenExists($user, $request->get('token', ''))) {
            $request->session()->flash('message', __('passwords.token'));
            $request->session()->flash('type', 'error');

            return redirect()->route('forgot');
        }

        return Inertia::render('Auth/ResetPassword', [
            'email' => $request->get('email'),
            'token' => $request->get('token'),
        ]);
    }

    /**
     * @function store new password
     */
    public function store(Request $request): RedirectResponse
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

        $status = Password::broker(config('fortify.passwords'))->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                Auth::guard(config('fortify.guard'))->login($user);
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            $request->session()->flash('message', __('passwords.reset'));
            $request->session()->flash('type', 'success');

            return redirect(config('fortify.home'));
        }

        return back()->withErrors(['email' => __($status)]);
    }

}
