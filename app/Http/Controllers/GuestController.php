<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuestController extends Controller
{
    /**
     * Display the about page.
     */
    public function about(Request $request): Response
    {
        return Inertia::render('Guest/About', [
            'request' => $request,
        ]);
    }

    /**
     * Display the imprint page.
     *
     * Renders the legally required imprint / Impressum for the site.
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
     */
    public function privacy(Request $request): Response
    {
        return Inertia::render('Guest/Privacy', [
            'request' => $request,
        ]);
    }
}
