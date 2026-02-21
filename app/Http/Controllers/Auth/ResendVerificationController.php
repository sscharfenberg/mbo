<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResendVerificationController extends Controller
{
    /**
     * @function show "resend verification email" page
     */
    public function show(Request $request): Response|RedirectResponse
    {
        return Inertia::render('Auth/ResendVerification');
    }

    /**
     * @function handle "resend verification email" request
     */
    public function store(Request $request): SymfonyResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:'.config('mbo.db.user.name.max'),
                    'min:'.config('mbo.db.user.name.min'),
                ],
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                ],
            ]);
        });

        $user = User::where('email', $request->email)
            ->where('name', $request->name)
            ->first();

        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification();
        }

        $request->session()->flash('message', __('passwords.verification_sent'));
        $request->session()->flash('type', 'success');

        return redirect('/');
    }
}
