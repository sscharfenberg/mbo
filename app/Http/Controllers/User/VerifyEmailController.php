<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyEmailController extends Controller
{
    /**
     * Mark a user's email address as verified.
     * Works without authentication by using the signed URL parameters.
     */
    public function __invoke(Request $request, string $id, string $hash): Response
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            abort(403, 'Invalid verification link.');
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        $request->session()->flash('message', __('auth.email_verified'));
        $request->session()->flash('type', 'success');

        return redirect()->route('login');
    }
}
