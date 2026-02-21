<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * Create the response for a successful registration.
     *
     * Logs the user back out immediately after Fortify's auto-login,
     * because the application requires email verification before allowing
     * access. Flashes a success message and redirects to the welcome page.
     *
     * @param  mixed  $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        // Fortify auto-logs in after registration; undo this since we
        // require email verification before allowing login.
        Auth::logout();

        $request->session()->flash('message', __('auth.registered'));
        $request->session()->flash('type', 'success');

        return redirect()->route('welcome');
    }
}
