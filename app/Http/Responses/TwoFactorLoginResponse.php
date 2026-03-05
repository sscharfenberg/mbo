<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;
use Laravel\Fortify\Fortify;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    /**
     * Create the response for a successful two-factor challenge.
     *
     * The 2FA challenge is submitted via fetch() with Accept: application/json,
     * so the same locale problem as LoginResponse applies: ConfigureLocale
     * middleware runs before the challenge is verified, leaving Auth::user()
     * null at that point and the locale at the browser/session default.
     * Re-set it from the user's stored preference before translating the flash.
     *
     * The flash must also be set before the wantsJson() branch for the same
     * reason as LoginResponse — the frontend drives navigation via
     * router.visit('/dashboard') and the session must already contain the
     * flash when that Inertia request arrives.
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
            app()->setLocale($user->locale);
        }

        $request->session()->flash('message', __('auth.logged_in'));
        $request->session()->flash('type', 'success');

        if ($request->wantsJson()) {
            return new JsonResponse('', 204);
        }

        return redirect()->intended(Fortify::redirects('login'));
    }
}