<?php

namespace App\Http\Controllers\Mbo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DecksController extends Controller
{
    /**
     * Display the user decks page.
     *
     * Renders the main decks view
     * with the current request context.
     *
     * @param  Request  $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Decks/Decks', []);
    }
}
