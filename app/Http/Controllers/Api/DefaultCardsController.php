<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DefaultCard;
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
        // Extract "set:xxx" (aliases: s:, e:) tokens from the query string.
        $setCode = null;
        $nameQ = preg_replace_callback(
            '/\b(?:set|s|e):(\S+)/i',
            function (array $m) use (&$setCode): string {
                $setCode = strtolower($m[1]);
                return '';
            },
            $q
        );

        // Extract "number:xxx" (alias: cn:) tokens from the query string.
        $collectorNumber = null;
        $nameQ = preg_replace_callback(
            '/\b(?:number|cn):(\S+)/i',
            function (array $m) use (&$collectorNumber): string {
                $collectorNumber = $m[1];
                return '';
            },
            (string) $nameQ
        );

        $nameQ = trim((string) preg_replace('/\s+/', ' ', $nameQ));

        // Require at least a filter or a name query of ≥ 2 characters.
        if (!$setCode && !$collectorNumber && mb_strlen($nameQ) < 2) {
            return null;
        }

        $query = DefaultCard::query();

        if ($setCode) {
            $query->whereHas('set', fn ($q) => $q->where('code', $setCode));
        }

        if ($collectorNumber) {
            $query->where('collector_number', $collectorNumber);
        }

        $segments = array_filter(explode(' ', $nameQ), fn (string $s) => $s !== '');
        foreach ($segments as $segment) {
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
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function artCropSearch(Request $request): JsonResponse
    {
        $query = $this->buildSearchQuery(trim($request->query('q', '')));

        if (!$query) {
            return response()->json([]);
        }

        $cards = $query->whereNotNull('art_crop')
            ->select('id', 'name', 'art_crop', 'set_id', 'artist_id')
            ->with('set:id,name,code', 'artist:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (DefaultCard $card) => [
                'id'       => $card->id,
                'name'     => $card->name,
                'art_crop' => $card->art_crop,
                'artist'   => $card->artist?->name,
                'set'      => $card->set ? [
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
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function searchCardImage(Request $request): JsonResponse
    {
        $query = $this->buildSearchQuery(trim($request->query('q', '')));

        if (!$query) {
            return response()->json([]);
        }

        $cards = $query->select('id', 'name', 'card_image_0', 'card_image_1', 'set_id', 'artist_id', 'collector_number')
            ->with('set:id,name,code', 'artist:id,name')
            ->orderBy('name')
            ->get()
            ->map(fn (DefaultCard $card) => [
                'id'            => $card->id,
                'name'          => $card->name,
                'card_image_0'  => $card->card_image_0,
                'card_image_1'  => $card->card_image_1,
                'artist'        => $card->artist?->name,
                'cn'            => $card->collector_number,
                'set'           => $card->set ? [
                    'name'      => $card->set->name,
                    'code'      => $card->set->code,
                ] : null,
            ]);

        return response()->json($cards);
    }

}
