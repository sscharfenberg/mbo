<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class StartController extends Controller
{

    /**
     * @function show "Start" page
     * @param \Illuminate\Http\Request $request
     * @return \Inertia\Response
     */
    public function show(\Illuminate\Http\Request $request): \Inertia\Response
    {
        return Inertia::render('Start/StartPage', [
            'request' => $request,
        ]);
    }

}
