<?php

namespace App\Http\Controllers\Collection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
