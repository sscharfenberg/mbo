<?php

namespace App\Http\Controllers\Decks;

use App\Enums\CardFormat;
use App\Http\Controllers\Controller;
use App\Models\Deck;
use App\Models\OracleCard;
use App\Services\DeckService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DecksController extends Controller
{
    /**
     * Display the user decks page.
     *
     * Queries all decks for the authenticated user with card counts and
     * last-activity timestamps, then groups them by format (alphabetical).
     * Decks within each format are sorted by last activity descending.
     */
    public function show(Request $request): Response
    {
        $decks = Deck::query()
            ->where('user_id', $request->user()->id)
            ->withCount(['deckCards', 'commanders'])
            ->addSelect([
                'last_card_update' => DB::table('deck_cards')
                    ->selectRaw('MAX(deck_cards.updated_at)')
                    ->whereColumn('deck_cards.deck_id', 'decks.id'),
                'last_commander_update' => DB::table('commanders')
                    ->selectRaw('MAX(commanders.updated_at)')
                    ->whereColumn('commanders.deck_id', 'decks.id'),
            ])
            ->get()
            ->each(function (Deck $deck) {
                $deck->card_count = $deck->deck_cards_count + $deck->commanders_count;
                $deck->last_activity = max(array_filter([
                    $deck->updated_at,
                    $deck->last_card_update,
                    $deck->last_commander_update,
                ]));
            })
            ->sortByDesc('last_activity');

        $grouped = $decks
            ->groupBy(fn (Deck $deck) => $deck->format->value)
            ->sortKeys()
            ->map(fn ($formatDecks) => $formatDecks->map(fn (Deck $deck) => [
                'id' => $deck->id,
                'name' => $deck->name,
                'state' => $deck->state->value,
                'visibility' => $deck->visibility->value,
                'colors' => $deck->colors,
                'card_count' => (int) $deck->card_count,
                'last_activity' => $deck->last_activity,
            ])->values());

        return Inertia::render('Decks/Decks', [
            'decksByFormat' => $grouped,
        ]);
    }

    /**
     * Validate and store a newly created deck.
     *
     * Wraps validation in a precognitive block so the frontend can perform
     * real-time field validation without triggering the actual store.
     * Command zone cards are validated structurally here (exists in oracle_cards),
     * domain validation (legal commander, valid pairing) is handled by DeckService.
     */
    public function store(Request $request): RedirectResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'format' => ['required', 'string', Rule::enum(CardFormat::class)],
                'deck_name' => ['required', 'string', 'max:'.Deck::NAME_MAX],
                'deck_description' => ['nullable', 'string', 'max:'.Deck::DESCRIPTION_MAX],
                'commander_id' => ['nullable', 'string', Rule::exists(OracleCard::class, 'id')],
                'companion_id' => ['nullable', 'string', Rule::exists(OracleCard::class, 'id')],
                'signature_spell_id' => ['nullable', 'string', Rule::exists(OracleCard::class, 'id')],
            ]);
        });

        $deck = DeckService::createDeck($request->user(), $request->only([
            'format', 'deck_name', 'deck_description', 'commander_id', 'companion_id', 'signature_spell_id',
        ]));

        $request->session()->flash('message', __('auth.deck_created', ['name' => $deck->name]));
        $request->session()->flash('type', 'success');

        return redirect(route('decks'));
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

        return Inertia::render('Decks/Create/CreateDeckPage', [
            'formats' => array_column(CardFormat::cases(), 'value'),
            'capabilities' => $capabilities,
            'nameMax' => Deck::NAME_MAX,
            'descriptionMax' => Deck::DESCRIPTION_MAX,
        ]);
    }
}
