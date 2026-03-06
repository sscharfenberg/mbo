<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteAccountController extends Controller
{
    /**
     * Permanently delete the authenticated user's account.
     *
     * Wraps validation in a precognitive block so the frontend can perform
     * real-time password validation without triggering the actual deletion.
     * On a real (non-precognitive) request the current-password rule confirms
     * the password, then the account is deleted, the session is invalidated,
     * and the user is redirected to the welcome page with a flash message.
     *
     * @param  Request  $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        $user = $request->user();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $user->delete();

        $request->session()->flash('message', __('auth.account_deleted'));
        $request->session()->flash('type', 'success');

        return redirect('/');
    }
}