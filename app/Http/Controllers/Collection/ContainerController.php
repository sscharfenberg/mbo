<?php

namespace App\Http\Controllers\Collection;

use App\Enums\BinderType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContainerController extends Controller
{
    /**
     * Display the "new container" page.
     *
     * Renders the "new container" form
     * with the current request context.
     *
     * @param  Request  $request
     * @return Response
     */
    public function showNew(Request $request): Response
    {
        return Inertia::render('Collection/Container/New/NewContainer', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
        ]);
    }
}
