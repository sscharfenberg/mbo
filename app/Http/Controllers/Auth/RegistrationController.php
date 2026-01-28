<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RegistrationController extends Controller
{
    /**
     * @function show "Registration" page
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Auth/Registration/RegistrationPage', [
            'request' => $request,
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create(Request $request)
    {
        // validate the request
        $validated = $request->validate([
            'name' => 'required',
            'email' => ['required', 'email:rfc,dns'],
            'password' => 'required',
        ]);

        // create user
        User::create($validated);

        // redirect
        return redirect('/');
    }
}
