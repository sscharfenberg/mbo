<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OracleCard;
use App\Services\CardSearchParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommanderController extends Controller
{
    /**
     * Search for cards that can be a commander.
     *
     * A card qualifies when at least one of its faces is either:
     *  - a legendary creature (type_line contains "Legendary" and has power + toughness), or
     *  - has "can be your commander" in its oracle text.
     *
     * Supports "set:xxx" and "number:xxx" tokens via CardSearchParser.
     */
    public function search(Request $request): JsonResponse
    {
        $parsed = CardSearchParser::parse(trim($request->query('q', '')));

        if (! $parsed) {
            return response()->json([]);
        }

        $query = OracleCard::query();

        if ($parsed['set_code']) {
            $query->whereHas('defaults', fn (Builder $q) => $q->whereHas(
                'set',
                fn (Builder $sq) => $sq->where('code', $parsed['set_code'])
            ));
        }

        foreach ($parsed['name_segments'] as $segment) {
            $query->where('name', 'like', "%$segment%");
        }

        // Must have at least one face that qualifies as a commander.
        $query->whereHas('faces', function (Builder $q): void {
            $q->where(function (Builder $q): void {
                // Legendary creature (has power + toughness).
                $q->where('type_line', 'like', '%Legendary%')
                    ->whereNotNull('power')
                    ->whereNotNull('toughness');
            })->orWhere('oracle_text', 'like', '%can be your commander%');
        });

        $cards = $query->select('id', 'name', 'color_identity')
            ->with(['faces' => fn ($q) => $q->select('oracle_card_id', 'face_index', 'mana_cost', 'type_line', 'oracle_text')
                ->orderBy('face_index')])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(function (OracleCard $card) {
                $allOracleText = $card->faces->pluck('oracle_text')->implode("\n");

                return [
                    'id' => $card->id,
                    'name' => $card->name,
                    'color_identity' => $card->color_identity,
                    'can_have_partner' => (bool) preg_match(
                        '/\bPartner\b|Friends forever|Doctor\'s companion|Choose a Background|Legendary partner/i',
                        $allOracleText
                    ),
                    'faces' => $card->faces->map(fn ($face) => [
                        'type_line' => $face->type_line,
                        'mana_cost' => $face->mana_cost,
                    ])->values(),
                ];
            });

        return response()->json($cards);
    }
}
