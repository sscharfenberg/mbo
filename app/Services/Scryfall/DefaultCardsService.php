<?php

namespace App\Services\Scryfall;

use App\Models\DefaultCard;
use App\Services\FormatService;
use App\Services\Scryfall\BulkdataService;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DefaultCardsService
{

    /**
     * Truncate the default_cards table before a fresh import.
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
    }

    /**
     * Persist a single default card to the database.
     *
     * Maps required Scryfall fields (prices, finishes, rarity, etc.) and
     * conditionally includes optional ones (oracle_id, layout). Image URIs
     * are resolved via BulkdataService::getImageUris().
     *
     * @param  array  $card  A single card object from the default_cards bulk JSON.
     * @return void
     */
    private function insertCard(array $card): void
    {
        $sis = new ScryfallImageService();
        // non nullable values
        $arr = [
            'id' => $card['id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'lang' => $card['lang'],
            'image_uris' => $sis->getImageUris($card), // actually nullable, but the function returns an empty array if no applicable values exist
            'art_crops' => $sis->getArtCrops($card),
            'finishes' => $card['finishes'],
            'games' => $card['games'],
            'prices' => $card['prices'],
            'digital' => $card['digital'],
            'rarity' => $card['rarity'],
            'set_id' => $card['set_id'],
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
        $f = new FormatService();
        $count = 0;
        Log::channel('scryfall')->notice("begin traversing $fileName.");
        JsonParser::parse(Storage::disk('scryfall-bulk')->get($fileName))->traverse(function (mixed $value, string|int $key, JsonParser $parser) use (&$count) {
            $this->insertCard($value);
            $count++;
        });
        $ms = $start->diffInMilliseconds(now());
        $numCards = number_format($count, 0, ",", ".");
        Log::channel('scryfall')->notice("finished inserting $numCards cards into database in ".$f->formatMs($ms).".");
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
        $bds = new BulkDataService();
        if (!$bds->prepareJson($type)) {
            Log::channel('scryfall')->error("error preparing '$type.json', aborting.");
            return; // error downloading file, abort
        }
        $this->preRunCleanup();
        $this->traverseJson($type.".json");
        $bds->postRunCleanup($type.".json");
    }

}
