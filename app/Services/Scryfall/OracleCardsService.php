<?php

namespace App\Services\Scryfall;

use App\Models\OracleCard;
use App\Services\FormatService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Support\Facades\Storage;

class OracleCardsService
{

    /**
     * the image formats that should be used, from highest priority to lowest.
     * @var string[]
     */
    protected array $imageFormats = ["large", "normal", "small", "png"];

    /**
     * @function cleanup after processing.
     * @param $fileName
     * @return void
     */
    private function postRunCleanup ($fileName): void
    {
        if (env('APP_ENV') == 'production') {
            Storage::disk('scryfall-bulk')->delete($fileName);
            Log::channel('scryfall')->notice("deleted '$fileName' from disk 'scryfall-bulk'.");
        }
    }

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
     * @function get the image uris for a card face
     * @param array $face
     * @return string
     */
    private function getCardFaceImageUris (array $face): string
    {
        foreach ($this->imageFormats as $format) { // loop formats and check if there is a image_uri key.
            if (array_key_exists($format, $face['image_uris'])) {
                return $face['image_uris'][$format];
            }
        }
        return "";
    }

    /**
     * @function get image uri from array
     * @param array $card
     * @return array
     */
    private function getImageUris (array $card): array
    {
        // if the card itself has an 'image_uris' array with length > 0, use the image from there
        if (array_key_exists('image_uris', $card) && count($card['image_uris']) > 0) {
            foreach ($this->imageFormats as $format) {
                if (array_key_exists($format, $card['image_uris'])) {
                    return [$card['image_uris'][$format]];
                }
            }
        }
        // if the card has at least one card_face, get the image_uris from there.
        else if (array_key_exists('card_faces', $card) && count($card['card_faces']) > 0) {
            $uris = [];
            foreach ($card['card_faces'] as $face) { // loop card faces
                if (
                    array_key_exists('image_uris', $face)
                    && count($face['image_uris']) > 0
                    && strlen($this->getCardFaceImageUris($face)) > 0
                ) {
                    $uris[] = $this->getCardFaceImageUris($face);
                }
            }
            return $uris;
        }
        return []; // no image uris at all.
    }

    /**
     * @function insert single oracle card into database
     * @param array $card
     * @return void
     */
    private function insertCard (array $card): void
    {
        // non nullable values
        $arr = [
            'id' => $card['id'],
            'name' => $card['name'],
            'collector_number' => $card['collector_number'],
            'layout' => $card['layout'],
            'type_line' => $card['type_line'],
            'lang' => $card['lang'],
            'cmc' => $card['cmc'],
            'legalities' => $card['legalities'],
            'image_uris' => $this->getImageUris($card),
            'reserved' => $card['reserved'],
            'game_changer' => $card['game_changer'],
            'scryfall_uri' => $card['scryfall_uri'],
        ];
        // nullable values
        if (array_key_exists('oracle_id', $card)) { $arr['oracle_id'] = $card['oracle_id']; }
        if (array_key_exists('mana_cost', $card)) { $arr['mana_cost'] = $card['mana_cost']; }
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
        if (Storage::disk('scryfall-bulk')->missing($type.".json")) {
            if (!$bds->downloadJson($type)) {
                Log::channel('scryfall')->error("error downloading '$type.json', aborting.");
                return; // error downloading file, abort
            }
        }
        $this->preRunCleanup();
        $this->traverseJson($type.".json");
        $this->postRunCleanup($type.".json");
    }

}
