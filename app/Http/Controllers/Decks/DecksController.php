<?php

namespace App\Http\Controllers\Decks;

use App\Enums\CardFormat;
use App\Http\Controllers\Controller;
use App\Models\Deck;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DecksController extends Controller
{
    /**
     * Display the user decks page.
     *
     * Renders the main decks view with the current request context.
     */
    public function show(Request $request): Response
    {
        return Inertia::render('Decks/Decks', []);
    }

    /**
     * Display the "create deck" page.
     */
    public function create(Request $request): Response
    {
        $capabilities = [];
        foreach (CardFormat::cases() as $format) {
            $capabilities[$format->value] = $format->rules()->toArray();
        }

        return Inertia::render('Decks/Add/AddDeckPage', [
            'formats' => array_column(CardFormat::cases(), 'value'),
            'capabilities' => $capabilities,
            'nameMax' => Deck::NAME_MAX,
            'descriptionMax' => Deck::DESCRIPTION_MAX,
        ]);
    }
}
