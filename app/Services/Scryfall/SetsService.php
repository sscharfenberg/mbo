<?php

namespace App\Services\Scryfall;

use App\Models\Set;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SetsService
{

    /**
     * Prepare the database and storage for a sets import.
     *
     * Always truncates the sets table. When running a full update,
     * also purges all cached set icon SVGs so they are re-downloaded.
     *
     * @param  bool  $full  Whether to also clear the set-icon storage disk.
     * @return void
     */
    private function setup(bool $full): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Set::truncate();
        Log::channel('scryfall')->debug("table 'sets' truncated.");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        // if it's a full update, clean "set-icon" disk.
        if ($full) {
            $files = Storage::disk('set-icon')->allFiles();
            Storage::disk('set-icon')->delete($files);
            Log::channel('scryfall')->debug("full update => ".count($files)." set icons deleted.");
        }
    }

    /**
     * Download a set icon SVG if it is not already cached locally.
     *
     * Returns the public path to the icon file on the "set-icon" disk,
     * regardless of whether a fresh download was needed.
     *
     * @param  string  $uri   The Scryfall icon_svg_uri for the set.
     * @param  string  $code  The set code, used as the local filename.
     * @return string  The public-facing path to the stored SVG.
     *
     * @throws ConnectionException
     */
    private function getSetIcon(string $uri, string $code): string
    {
        $fileName = $code.".svg";
        if (Storage::disk('set-icon')->missing($fileName)) {
            $response = Http::withHeaders(config('mbo.scryfall.header'))
                ->get($uri);
            if ($response->successful()) {
                Storage::disk('set-icon')->put($fileName, $response->body());
                Log::channel('scryfall')->debug("created SVG in storage disk 'set-icon': $fileName");
            } else {
                Log::channel('scryfall')->error("error calling icon uri '$uri' from scryfall: ".$response->body());
            }
        }
        return "/set-icon/".$fileName;
    }

    /**
     * Persist a single set from the Scryfall API response to the database.
     *
     * Maps required fields directly and conditionally includes optional
     * fields (block_code, released_at, etc.) only when present.
     * Also downloads the set icon via getSetIcon().
     *
     * @param  array  $set  A single set object from the Scryfall /sets response.
     * @return void
     *
     * @throws ConnectionException
     */
    private function insertSet(array $set): void
    {
        // these array keys always exist.
        $arr = [
            'id' => $set['id'],
            'code' => $set['code'],
            'name' => $set['name'],
            'scryfall_uri' => $set['scryfall_uri'],
            'set_type' => $set['set_type'],
            'icon' => $this->getSetIcon($set['icon_svg_uri'], $set['code']),
            'card_count' => $set['card_count'],
            'digital' => $set['digital'],
        ];
        // these array keys might not exist.
        if (array_key_exists('block_code', $set)) { $arr['block_code'] = $set['block_code']; }
        if (array_key_exists('released_at', $set)) { $arr['released_at'] = $set['released_at']; }
        if (array_key_exists('block', $set)) { $arr['block'] = $set['block']; }
        if (array_key_exists('parent_set_code', $set)) { $arr['parent_set_code'] = $set['parent_set_code']; }
        if (array_key_exists('printed_size', $set)) { $arr['printed_size'] = $set['printed_size']; }
        $newSet = Set::create($arr);
        if ($newSet->wasRecentlyCreated) {
            Log::channel('scryfall')->debug("created set [$newSet->code] $newSet->name.");
        }
    }

    /**
     * Fetch all sets from the Scryfall API and replace the local database.
     *
     * Filters out sets with zero cards. Runs setup() first to truncate
     * existing data (and optionally clear cached icons on a full update).
     *
     * @param  bool  $full  Whether to also purge cached set icons before importing.
     * @return void
     */
    public function updateSets(bool $full): void
    {
        $this->setup($full);
        try {
            $response = Http::withHeaders(config('mbo.scryfall.header'))
                ->get('https://api.scryfall.com/sets');
            if ($response->successful()) {
                $sets = $response->json();
                if (array_key_exists('data', $sets)) { // all seems fine, proceed with updating tb
                    $sets = collect($sets['data'])->filter(function ($set) {
                        return $set['card_count'] > 0;
                    });
                    $sets->each(function ($set) {
                        $this->insertSet($set);
                    });
                } else { // json does not have 'data' prop
                    Log::channel('scryfall')->error("Scryfall response successful, but json does not have a field 'data'.");
                }
            } else { // scryfall response not successful
                Log::channel('scryfall')->error("Scryfall response failed: ".$response->body());
            }
        } catch (\Exception $exception) {
            Log::channel('scryfall')->error($exception->getMessage());
            report($exception);
        }
    }

}
