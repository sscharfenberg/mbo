<?php

namespace App\Services\Scryfall;

use App\Models\OracleCard;
use App\Services\FormatService;
use App\Services\Scryfall\BulkdataService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\Storage;

class OracleCardsService
{

    /**
     * @function truncate oracle_cards table
     * @return void
     */
    private function preRunCleanup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        OracleCard::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        Log::channel('scryfall')->notice("truncated oracle_cards table.");
    }

    /**
     * @function insert single oracle card into database
     * @param array $card
     * @return void
     */
    private function insertCard (array $card): void
    {
        $bds = new BulkdataService();
        // non nullable values
        $arr = [
            'id' => $card['oracle_id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'type_line' => $card['type_line'],
            'lang' => $card['lang'],
            'cmc' => $card['cmc'],
            'legalities' => $card['legalities'],
            'image_uris' => $bds->getImageUris($card), // actually nullable, but the function returns an empty array if no applicable values exist
            'reserved' => $card['reserved'],
            'game_changer' => $card['game_changer'],
            'scryfall_uri' => $card['scryfall_uri'],
        ];
        // nullable values
        if (array_key_exists('mana_cost', $card)) { $arr['mana_cost'] = $card['mana_cost']; }
        if (array_key_exists('layout', $card)) { $arr['layout'] = $card['layout']; }
        if (array_key_exists('colors', $card) && count($card['colors']) > 0) {
            $arr['colors'] = implode("", $card['colors']);
        }
        if (array_key_exists('color_identity', $card) && count($card['color_identity']) > 0) {
            $arr['color_identity'] = implode("", $card['color_identity']);
        }
        // insert into db
        try {
            $newCard = OracleCard::create($arr);
            if ($newCard->wasRecentlyCreated) {
                Log::channel('scryfall')->debug("Inserted OracleCard \"".$newCard->name."\".");
            }
        } catch (\Exception $e) {
            Log::channel('scryfall')->error("error inserting card ".$card['name'].": ".$e->getMessage());
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
        Log::channel('scryfall')->notice("begin traversing oracle cards json.");
        JsonParser::parse(Storage::disk('scryfall-bulk')->get($fileName))->traverse(function (mixed $value, string|int $key, JsonParser $parser) use (&$count) {
            $this->insertCard($value);
            $count++;
        });
        $ms = $start->diffInMilliseconds(now());
        $numCards = number_format($count, 0, ",", ".");
        Log::channel('scryfall')->notice("finished inserting $numCards oracle cards into database in ".$f->formatMs($ms).".");
    }

    /**
     * @function download and analyze bulk data from json and update database for "oracle_cards"
     * @return void
     */
    public function updateOracleCards(): void
    {
        $type = "oracle_cards";
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
