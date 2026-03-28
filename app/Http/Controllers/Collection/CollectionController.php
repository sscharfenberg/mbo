<?php

namespace App\Http\Controllers\Collection;

use App\Enums\Scryfall\ScryfallRarity;
use App\Http\Controllers\Controller;
use App\Models\CardStack;
use App\Services\ContainerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CollectionController extends Controller
{
    /**
     * Display the user collection page.
     *
     * Renders the main collection view with collection-wide statistics.
     */
    public function list(Request $request): Response
    {
        $user = $request->user();
        $currency = $user->currency;
        $unitPriceSql = ContainerService::unitPriceSql($currency);

        $baseQuery = CardStack::query()
            ->join('default_cards', 'card_stacks.default_card_id', '=', 'default_cards.id')
            ->join('containers', 'card_stacks.container_id', '=', 'containers.id')
            ->where('containers.user_id', $user->id);

        $stats = (clone $baseQuery)
            ->selectRaw('COALESCE(SUM(card_stacks.amount), 0) as total_cards')
            ->selectRaw('COUNT(*) as total_stacks')
            ->selectRaw("COALESCE(SUM(card_stacks.amount * ({$unitPriceSql})), 0) as total_price")
            ->selectRaw('COALESCE(SUM(CASE WHEN default_cards.rarity = ? THEN card_stacks.amount ELSE 0 END), 0) as commons', [ScryfallRarity::Common->value])
            ->selectRaw('COALESCE(SUM(CASE WHEN default_cards.rarity = ? THEN card_stacks.amount ELSE 0 END), 0) as uncommons', [ScryfallRarity::Uncommon->value])
            ->selectRaw('COALESCE(SUM(CASE WHEN default_cards.rarity = ? THEN card_stacks.amount ELSE 0 END), 0) as rares', [ScryfallRarity::Rare->value])
            ->selectRaw('COALESCE(SUM(CASE WHEN default_cards.rarity = ? THEN card_stacks.amount ELSE 0 END), 0) as mythics', [ScryfallRarity::Mythic->value])
            ->first();

        $containerCount = $user->containers()->count();

        return Inertia::render('Collection/CollectionPage', [
            'stats' => [
                'totalCards' => (int) $stats->total_cards,
                'totalStacks' => (int) $stats->total_stacks,
                'totalPrice' => (float) $stats->total_price,
                'containers' => $containerCount,
                'commons' => (int) $stats->commons,
                'uncommons' => (int) $stats->uncommons,
                'rares' => (int) $stats->rares,
                'mythics' => (int) $stats->mythics,
            ],
        ]);
    }
}
