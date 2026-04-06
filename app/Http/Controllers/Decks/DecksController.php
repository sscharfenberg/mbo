<?php

namespace App\Http\Controllers\Decks;

use App\Enums\CardFormat;
use App\Http\Controllers\Controller;
use App\Models\Deck;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
     * Validate the deck form fields.
     *
     * Wraps validation in a precognitive block so the frontend can perform
     * real-time field validation without triggering the actual store.
     */
    public function store(Request $request): void
    {
        precognitive(function () use ($request) {
            $request->validate([
                'format' => ['required', 'string', Rule::enum(CardFormat::class)],
                'deck_name' => ['required', 'string', 'max:'.Deck::NAME_MAX],
                'deck_description' => ['nullable', 'string', 'max:'.Deck::DESCRIPTION_MAX],
            ]);
        });

        // TODO: store deck
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
