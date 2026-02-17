<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    /**
     * @function response for successful registration
     * @param $request
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
