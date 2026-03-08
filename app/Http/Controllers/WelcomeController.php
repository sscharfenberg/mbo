<?php

namespace App\Http\Controllers;

use App\Models\BulkData;
use App\Models\DefaultCard;
use App\Models\OracleCard;
use App\Models\Set;
use App\Models\Symbol;
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
        return Inertia::render('Guest/Welcome/Welcome', [
            'oracleCards' => [
                'num' => OracleCard::count(),
                'size' => BulkData::where('type', 'oracle_cards')->first()?->size,
            ],
            'defaultCards' => [
                'num' => DefaultCard::count(),
                'size' => BulkData::where('type', 'default_cards')->first()?->size,
            ],
            'sets' => Set::count(),
            'symbols' => Symbol::where('funny', false)->inRandomOrder()->limit(10)->get(['path', 'english']),
        ]);
    }

}
