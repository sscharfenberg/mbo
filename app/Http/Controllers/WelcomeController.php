<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{

    /**
     * Display the welcome / landing page.
     *
     * Public entry point of the application, shown to unauthenticated visitors.
     *
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request): \Inertia\Response
    {
        return Inertia::render('Guest/Welcome', [
            'request' => $request,
        ]);
    }

}
