<?php

namespace App\Http\Controllers\Api;

use App\Enums\CardFormat;
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
     *
     * Required query parameters:
     *  - `format=<key>` — CardFormat enum value; filters to cards legal/restricted in this format.
     *
     * Optional query parameters:
     *  - `rule0=1`              — skip commander-legality filters (any oracle card matches).
     *  - `partner=1`            — restrict to cards with the Partner keyword.
     *  - `friends_forever=1`    — restrict to cards with "Friends forever".
     *  - `doctors_companion=1`  — restrict to cards with "Doctor's companion".
     *  - `background=1`         — restrict to cards with the Background subtype.
     *  - `exclude=<uuid>`       — exclude a specific oracle card (e.g. the already-selected commander).
     */
    public function search(Request $request): JsonResponse
    {
        $format = CardFormat::tryFrom($request->query('format', ''));

        if (! $format) {
            return response()->json([]);
        }

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

        if ($request->filled('exclude')) {
            $query->where('id', '!=', $request->query('exclude'));
        }

        // Rule 0: skip commander-legality and format-legality filters when the user opts in.
        if (! $request->boolean('rule0')) {
            $query->legalIn($format);

            // Background search has its own type-line filter — skip the commander constraint.
            if (! $request->boolean('background')) {
                // Must qualify as a commander: front face is a legendary creature,
                // or any face explicitly says "can be your commander".
                $query->where(function (Builder $q): void {
                    $q->whereHas('faces', function (Builder $fq): void {
                        $fq->where('face_index', 0)
                            ->where('type_line', 'like', '%Legendary%')
                            ->whereNotNull('power')
                            ->whereNotNull('toughness');
                    })->orWhereHas('faces', function (Builder $fq): void {
                        $fq->where('oracle_text', 'like', '%can be your commander%');
                    });
                });
            }
        }

        // When searching for a partner, only return cards with a partner keyword.
        if ($request->boolean('partner')) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('oracle_text', 'regexp', '\\bPartner\\b|Doctor\'s companion|Legendary partner');
            });
        }

        // When searching for a Doctor's companion, only return cards with that keyword.
        if ($request->boolean('doctors_companion')) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('oracle_text', 'like', '%Doctor\'s companion%');
            });
        }

        // When searching for a "Friends forever" partner, only return other "Friends forever" cards.
        if ($request->boolean('friends_forever')) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('oracle_text', 'like', '%Friends forever%');
            });
        }

        // When searching for a background, only return cards with the Background subtype.
        if ($request->boolean('background')) {
            $query->whereHas('faces', function (Builder $fq): void {
                $fq->where('type_line', 'like', '%Background%');
            });
        }

        $cards = $query->select('id', 'name', 'color_identity')
            ->with(['faces' => fn ($q) => $q->select('oracle_card_id', 'face_index', 'mana_cost', 'type_line', 'oracle_text')
                ->orderBy('face_index')])
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->map(function (OracleCard $card) {
                $allOracleText = $card->faces->pluck('oracle_text')->implode("\n");

                $frontTypeLine = $card->faces->first()?->type_line ?? '';
                $companion = $this->resolveCompanionType($allOracleText, $frontTypeLine);

                return [
                    'id' => $card->id,
                    'name' => $card->name,
                    'color_identity' => $card->color_identity,
                    'companion_type' => $companion['type'],
                    'partner_with_name' => $companion['partner_with_name'],
                    'faces' => $card->faces->map(fn ($face) => [
                        'type_line' => $face->type_line,
                        'mana_cost' => $face->mana_cost,
                    ])->values(),
                ];
            });

        return response()->json($cards);
    }

    /**
     * Determine the companion type from the combined oracle text and front-face type line.
     *
     * @return array{type: 'partner'|'partner_with'|'friends_forever'|'doctors_companion'|'background'|null, partner_with_name: string|null}
     */
    private function resolveCompanionType(string $oracleText, string $frontTypeLine): array
    {
        if (preg_match('/Choose a Background/i', $oracleText)) {
            return ['type' => 'background', 'partner_with_name' => null];
        }

        if (preg_match('/Partner with ([^\n(]+)/i', $oracleText, $matches)) {
            return ['type' => 'partner_with', 'partner_with_name' => trim($matches[1])];
        }

        if (preg_match('/Friends forever/i', $oracleText)) {
            return ['type' => 'friends_forever', 'partner_with_name' => null];
        }

        // Time Lord Doctors can have a Doctor's companion in the command zone.
        if ($frontTypeLine === 'Legendary Creature — Time Lord Doctor') {
            return ['type' => 'doctors_companion', 'partner_with_name' => null];
        }

        if (preg_match('/\bPartner\b|Legendary partner/i', $oracleText)) {
            return ['type' => 'partner', 'partner_with_name' => null];
        }

        return ['type' => null, 'partner_with_name' => null];
    }
}
