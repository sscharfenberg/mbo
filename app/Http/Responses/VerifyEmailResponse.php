<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    /**
     * Create the response for a successful email verification.
     *
     * Flashes a success message and redirects to the login page so the
     * user can sign in with their now-verified account. Returns a 204
     * for JSON/API consumers.
     *
     * @param  mixed  $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        $request->session()->flash('message', __('auth.email_verified'));
        $request->session()->flash('type', 'success');

        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->route('login');
    }
}
