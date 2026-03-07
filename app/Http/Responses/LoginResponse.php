<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;

class LoginResponse implements LoginResponseContract
{
    /**
     * Create the response for a successful login.
     *
     * The login form submits via fetch() with Accept: application/json so that
     * Fortify returns { two_factor: true } instead of redirecting, keeping the
     * 2FA challenge on the same page. This means ConfigureLocale middleware
     * cannot resolve the user's locale on this request — Auth::user() is null
     * when middleware runs, before Fortify's authentication pipeline completes.
     * By the time this response is built, the user is authenticated and
     * $request->user() is populated, so we set the locale here explicitly
     * before translating the flash message.
     *
     * The flash is set before the wantsJson() branch because the frontend
     * handles navigation itself (router.visit('/dashboard')), so there is no
     * server-side redirect to carry the flash — it must already be in the
     * session when the subsequent Inertia page request is made.
     *
     * @param  mixed  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // ConfigureLocale runs before authentication completes, so the locale
        // defaults to browser/session. Re-set it from the user's stored preference
        // now that $request->user() is available.
        if ($user = $request->user()) {
            app()->setLocale($user->locale->value);
        }

        $request->session()->flash('message', __('auth.logged_in'));
        $request->session()->flash('type', 'success');

        if ($request->wantsJson()) {
            return response()->json(['two_factor' => false]);
        }

        return redirect()->intended(Fortify::redirects('login'));
    }
}