<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $request->session()->flash('message', __('auth.registered'));
        $request->session()->flash('type', 'success');

        return redirect()->intended(config('fortify.home'));
    }
}
