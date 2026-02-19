<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ForgotUsernameNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Inertia\Response;

class ForgotController extends Controller
{
    /**
     * @function show "Forgot" page
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Auth/Forgot');
    }

    /**
     * @function handle "Forgot password" or "Forgot username" submission
     */
    public function store(Request $request): RedirectResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'type' => ['required', 'in:password,name'],
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                ],
            ]);
        });

        return match ($request->type) {
            'password' => $this->sendPasswordResetLink($request),
            'name' => $this->sendUsernameReminder($request),
        };
    }

    private function sendPasswordResetLink(Request $request): RedirectResponse
    {
        Password::broker(config('fortify.passwords'))
            ->sendResetLink($request->only('email'));

        // Always return success to prevent email enumeration
        $request->session()->flash('message', __('passwords.sent'));
        $request->session()->flash('type', 'success');

        return back();
    }

    private function sendUsernameReminder(Request $request): RedirectResponse
    {
        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->notify(new ForgotUsernameNotification());
        }

        $request->session()->flash('message', __('passwords.username_sent'));
        $request->session()->flash('type', 'success');

        return back();
    }
}
