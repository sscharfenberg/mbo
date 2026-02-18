<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ForgotController extends Controller
{
    /**
     * @function show "Forgot" page
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Auth/Forgot', [
            'request' => $request,
        ]);
    }
}
