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
     * Prepare the database for a sets import by truncating the sets table.
     *
     * Icon SVGs are intentionally not purged here — set icons are stable once
     * released and getSetIcon() only downloads files that are missing, so
     * wiping cached icons would cause unnecessary re-downloads on every run.
     *
     * @return void
     */
    private function setup(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Set::truncate();
        Log::channel('scryfall')->debug("table 'sets' truncated.");
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Download a set icon SVG if it is not already cached locally.
     *
     * Returns the public path to the icon file on the "set" disk,
     * regardless of whether a fresh download was needed.
     *
     * @param  string  $uri   The Scryfall icon_svg_uri for the set.
     * @param  string  $code  The set code, used as the local filename.
     * @return string  The public-facing path to the stored SVG.
     *
     * @throws ConnectionException
     */
    private function buildFileName(string $code): string
    {
        return $code.'.svg';
    }

    private function getSetIcon(string $uri, string $code): string
    {
        $fileName = $this->buildFileName($code);
        if (Storage::disk('set')->missing($fileName)) {
            $response = Http::withHeaders(config('mbo.scryfall.header'))
                ->get($uri);
            if ($response->successful()) {
                Storage::disk('set')->put($fileName, $response->body());
                Log::channel('scryfall')->debug("created SVG in storage disk 'set': $fileName");
            } else {
                Log::channel('scryfall')->error("error calling icon uri '$uri' from scryfall: ".$response->body());
            }
        }
        return "/set/".$fileName;
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
            'path' => $this->getSetIcon($set['icon_svg_uri'], $set['code']),
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
     * Filters out sets with zero cards. Runs setup() first to truncate existing data.
     *
     * @return void
     */
    public function updateSets(): void
    {
        $this->setup();
        try {
            $response = Http::withHeaders(config('mbo.scryfall.header'))
                ->get('https://api.scryfall.com/sets');
            if ($response->successful()) {
                $sets = $response->json();
                if (array_key_exists('data', $sets)) { // all seems fine, proceed with updating tb
                    $sets = collect($sets['data'])->filter(function ($set) {
                        return $set['card_count'] > 0;
                    });
                    $sets->each(fn($set) => $this->insertSet($set));
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
