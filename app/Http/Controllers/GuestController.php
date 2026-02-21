<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{

    /**
     * Display the imprint page.
     *
     * Renders the legally required imprint / Impressum for the site.
     *
     * @param  Request  $request
     * @return Response
     */
    public function imprint(Request $request): Response
    {
        return Inertia::render('Guest/Imprint', [
            'request' => $request,
        ]);
    }

    /**
     * Display the privacy policy page.
     *
     * Renders the legally required privacy policy / Datenschutzerklärung.
     *
     * @param  Request  $request
     * @return Response
     */
    public function privacy(Request $request): Response
    {
        return Inertia::render('Guest/Privacy', [
            'request' => $request,
        ]);
    }

}
