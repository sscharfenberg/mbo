<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{

    /**
     * @function show "Imprint" page
     * @param Request $request
     * @return Response
     */
    public function imprint(Request $request): Response
    {
        return Inertia::render('Guest/ImprintPage', [
            'request' => $request,
        ]);
    }

    /**
     * @function show "Privacy" page
     * @param Request $request
     * @return Response
     */
    public function privacy(Request $request): Response
    {
        return Inertia::render('Guest/PrivacyPage', [
            'request' => $request,
        ]);
    }

}
