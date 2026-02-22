<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\ProfileInformationUpdatedResponse as ProfileInformationUpdatedResponseContract;

class ProfileInformationUpdatedResponse implements ProfileInformationUpdatedResponseContract
{
    /**
     * Create the response for a successful profile information update.
     *
     * When the email changed the user's verification is cleared, so we
     * log them out and redirect to the login page. Otherwise we simply
     * redirect back with a success flash.
     *
     * @param  mixed  $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse('', 200);
        }

        $emailChanged = is_null($request->user()->email_verified_at);

        if ($emailChanged) {
            Auth::logout();

            $request->session()->flash('message', __('auth.profile_updated_email'));
            $request->session()->flash('type', 'success');

            return redirect()->route('login');
        }

        $request->session()->flash('message', __('auth.profile_updated'));
        $request->session()->flash('type', 'success');

        return back();
    }
}
