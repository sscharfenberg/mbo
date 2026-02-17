<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\VerifyEmailResponse as VerifyEmailResponseContract;

class VerifyEmailResponse implements VerifyEmailResponseContract
{
    /**
     * @function response for successful email verification
     * @param $request
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
