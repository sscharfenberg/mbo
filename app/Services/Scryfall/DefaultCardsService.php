<?php

namespace App\Services\Scryfall;

use App\Models\DefaultCard;
use App\Services\FormatService;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DefaultCardsService
{

    private ScryfallImageService $imageService;
    private ArtistsService $artistsService;
    private FormatService $formatService;
    private BulkdataService $bulkdataService;

    public function __construct()
    {
        $this->imageService = new ScryfallImageService();
        $this->artistsService = new ArtistsService();
        $this->formatService = new FormatService();
        $this->bulkdataService = new BulkdataService();
    }

    /**
     * Truncate the default_cards and artists tables before a fresh import.
     *
     * Temporarily disables foreign key checks to allow truncation.
     *
     * @return void
     */
    private function preRunCleanup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DefaultCard::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel('scryfall')->notice("truncated default_cards table.");
        $this->artistsService->truncate();
    }

    /**
     * Persist a single default card to the database.
     *
     * Maps required Scryfall fields (prices, finishes, rarity, etc.) and
     * conditionally includes optional ones (oracle_id, layout, artist_id).
     * Image URIs are resolved via ScryfallImageService.
     *
     * @param  array  $card  A single card object from the default_cards bulk JSON.
     * @return void
     */
    private function insertCard(array $card): void
    {
        // non nullable values
        $arr = [
            'id' => $card['id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'lang' => $card['lang'],
            'image_uris' => $this->imageService->getImageUris($card),
            'art_crop' => $this->imageService->getArtCrop($card),
            'finishes' => $card['finishes'],
            'games' => $card['games'],
            'prices' => $card['prices'],
            'digital' => $card['digital'],
            'rarity' => $card['rarity'],
            'set_id' => $card['set_id'],
            'artist_id' => $this->artistsService->resolveArtistId($card['artist'] ?? null),
        ];
        // nullable values
        if (array_key_exists('oracle_id', $card)) { $arr['oracle_id'] = $card['oracle_id']; }
        if (array_key_exists('layout', $card)) { $arr['layout'] = $card['layout']; }
        // insert into db
        try {
            DefaultCard::create($arr);
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("error inserting DefaultCard [".strtoupper($card['set'])."] ".$card['name'].": ".$e->getMessage());
            Log::channel('scryfall')->error($e->getTraceAsString());
        }
    }

    /**
     * Stream-parse the bulk JSON file and insert each card.
     *
     * Uses JsonParser to avoid loading the entire file into memory,
     * which is critical for the large Scryfall bulk exports.
     *
     * @param  string  $fileName  The filename on the "scryfall-bulk" disk.
     * @return void
     */
    private function traverseJson($fileName): void
    {
        $start = now();
        $count = 0;
        Log::channel('scryfall')->notice("begin traversing $fileName.");
        JsonParser::parse(Storage::disk('scryfall-bulk')->get($fileName))->traverse(function (mixed $value, string|int $key, JsonParser $parser) use (&$count) {
            $this->insertCard($value);
            $count++;
        });
        $ms = $start->diffInMilliseconds(now());
        $numCards = number_format($count, 0, ",", ".");
        Log::channel('scryfall')->notice("finished inserting $numCards cards into database in ".$this->formatService->formatMs($ms).".");
    }

    /**
     * Run a full default-cards import from Scryfall.
     *
     * Downloads the "default_cards" bulk JSON (if not already cached),
     * truncates the existing data, streams through every card to insert
     * it, and cleans up the downloaded file afterwards.
     *
     * @return void
     */
    public function updateAllCards(): void
    {
        $type = "default_cards";
        if (!$this->bulkdataService->prepareJson($type)) {
            Log::channel('scryfall')->error("error preparing '$type.json', aborting.");
            return; // error downloading file, abort
        }
        $this->preRunCleanup();
        $this->traverseJson($type.".json");
        $this->bulkdataService->postRunCleanup($type.".json");
    }

}
