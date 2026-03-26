<?php

namespace App\Http\Controllers\Api;

use App\Enums\Finish;
use App\Http\Controllers\Controller;
use App\Models\DefaultCard;
use App\Services\CardSearchParser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DefaultCardsController extends Controller
{
    /**
     * Parse the search query and build a base DefaultCard query filtered by
     * name segments and optional "set:xxx" / "number:xxx" tokens.
     *
     * Returns null when the input is too short to run a meaningful search
     * (no set/number filter and name query < 2 characters).
     *
     * @param  string  $q  Raw query string from the request.
     * @return Builder<DefaultCard>|null
     */
    private function buildSearchQuery(string $q): ?Builder
    {
        $parsed = CardSearchParser::parse($q);

        if (! $parsed) {
            return null;
        }

        $query = DefaultCard::query();

        if ($parsed['set_code']) {
            $query->whereHas('set', fn ($q) => $q->where('code', $parsed['set_code']));
        }

        if ($parsed['collector_number']) {
            $query->where('collector_number', $parsed['collector_number']);
        }

        foreach ($parsed['name_segments'] as $segment) {
            $query->where('name', 'like', "%$segment%");
        }

        return $query;
    }

    /**
     * Search default_cards by name (and optionally set code) and return id + first art crop.
     *
     * Supports "set:xxx" and "number:xxx" tokens in the query string, e.g.:
     *   "sol ring set:lea"  →  name LIKE %sol% AND name LIKE %ring% AND set.code = 'lea'
     *   "set:lea"           →  all cards from set 'lea'
     *   "number:123"        →  collector_number = '123'
     */
    public function artCropSearch(Request $request): JsonResponse
    {
        $query = $this->buildSearchQuery(trim($request->query('q', '')));

        if (! $query) {
            return response()->json([]);
        }

        $cards = $query->whereNotNull('art_crop')
            ->select('id', 'name', 'art_crop', 'set_id', 'artist_id')
            ->with('set:id,name,code', 'artist:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (DefaultCard $card) => [
                'id' => $card->id,
                'name' => $card->name,
                'art_crop' => $card->art_crop,
                'artist' => $card->artist?->name,
                'set' => $card->set ? [
                    'name' => $card->set->name,
                    'code' => $card->set->code,
                ] : null,
            ]);

        return response()->json($cards);
    }

    /**
     * Search default_cards by name (and optionally set code) and return card image faces.
     *
     * Supports "set:xxx" and "number:xxx" tokens in the query string, e.g.:
     *   "sol ring set:lea"  →  name LIKE %sol% AND name LIKE %ring% AND set.code = 'lea'
     *   "number:123"        →  collector_number = '123'
     */
    public function searchCardImage(Request $request): JsonResponse
    {
        $query = $this->buildSearchQuery(trim($request->query('q', '')));

        if (! $query) {
            return response()->json([]);
        }

        $cards = $query->select('id', 'name', 'card_image_0', 'card_image_1', 'set_id', 'artist_id', 'collector_number', 'finishes')
            ->with('set:id,name,code', 'artist:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (DefaultCard $card) => [
                'id' => $card->id,
                'name' => $card->name,
                'card_image_0' => $card->card_image_0,
                'card_image_1' => $card->card_image_1,
                'artist' => $card->artist?->name,
                'cn' => $card->collector_number,
                'finishes' => Finish::labelsFromMask($card->finishes),
                'set' => $card->set ? [
                    'name' => $card->set->name,
                    'code' => $card->set->code,
                ] : null,
            ]);

        return response()->json($cards);
    }
}
