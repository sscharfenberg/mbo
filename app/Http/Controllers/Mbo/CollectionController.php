<?php

namespace App\Http\Controllers\Mbo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Fortify\Features;

class CollectionController extends Controller
{
    /**
     * Display the user collection page.
     *
     * Renders the main collection view
     * with the current request context.
     *
     * @param  Request  $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Collection/Collection', []);
    }
}
