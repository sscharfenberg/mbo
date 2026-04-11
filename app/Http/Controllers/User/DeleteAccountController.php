<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteAccountController extends Controller
{
    /**
     * Permanently delete the authenticated user's account.
     *
     * Validates the current password, logs the user out, invalidates the
     * session, and deletes the user record. Responds with JSON when the
     * request expects it (so the modal can avoid a full Inertia visit), or
     * with a redirect to the welcome page otherwise.
     */
    public function destroy(Request $request): JsonResponse|RedirectResponse
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

        if ($request->expectsJson()) {
            return response()->json(['redirect' => '/']);
        }

        return redirect('/');
    }
}
