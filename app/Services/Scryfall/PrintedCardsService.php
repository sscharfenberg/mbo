<?php

namespace App\Services\Scryfall;

use App\Models\PrintedCard;
use App\Services\FormatService;
use App\Services\Scryfall\BulkdataService;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PrintedCardsService
{

    /**
     * @function truncate oracle_cards table
     * @return void
     */
    private function preRunCleanup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PrintedCard::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel('scryfall')->notice("truncated printed_cards table.");
    }

    /**
     * @function insert single PrintedCard into database
     * @param array $card
     * @return void
     */
    private function insertCard(array $card): void
    {
        $bds = new BulkdataService();
        // non nullable values
        $arr = [
            'id' => $card['id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'lang' => $card['lang'],
            'image_uris' => $bds->getImageUris($card), // actually nullable, but the function returns an empty array if no applicable values exist
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
            PrintedCard::create($arr);
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("error inserting PrintedCard [".strtoupper($card['set'])."] ".$card['name'].": ".$e->getMessage());
            Log::channel('scryfall')->error($e->getTraceAsString());
        }
    }

    /**
     * @function loop all entries of the json file
     * @param $fileName
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
     * @function download and analyze bulk data from json and update database for "oracle_cards"
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
