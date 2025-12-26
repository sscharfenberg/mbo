<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Cerbero\JsonParser\JsonParser;

class GetBulkData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:bulk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get bulk data from scryfall';


    /**
     * @function get bulk metadata from scryfall.
     * @return void
     */
    private function getBulkMetadata(): void
    {
        Log::channel('scryfall')->debug('Requesting metadata from scryfall.');
        $this->comment('Requesting metadata from scryfall.');
        try {
            $response = Http::accept('application/json')
                ->get('https://api.scryfall.com/bulk-data');
            if ($response->successful()) {
                $json = $response->json();
                Log::channel('scryfall')->debug('API request to scryfall successful.: '.json_encode($json, JSON_PRETTY_PRINT));
                $this->comment('API request to scryfall successful.');
                $scryfallData = collect($json['data']);
                $oracleCards = $scryfallData->filter(function ($item) {
                    return $item['type'] == 'oracle_cards';
                })->first();
                $this->getOracleCards((array)$oracleCards);
            }
            if ($response->failed()) {
                Log::channel('scryfall')->error('Failed scryfall api request: '.$response->body());
                $this->error('Failed scryfall api request: '.$response->body());
            }
        } catch (\Exception $exception) {
            Log::channel('scryfall')->error($exception->getMessage());
        }
    }

    // use https://github.com/cerbero90/json-parser
    private function getOracleCards (array $oracleCards): void
    {
        $url = $oracleCards['download_uri'];
        Log::channel('scryfall')->debug('Scryfall download uri for oracle cards: '.$url);
        $this->line('Scryfall download uri for oracle cards: '.$url);
        JsonParser::parse($url)->traverse(function (mixed $value, string|int $key, JsonParser $parser) {
            // lazily load one key and value at a time, we can also access the parser if needed
            dd($value);
        });
    }




    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fd = new FormatService();
        $start = now();
        $this->info("artisan command 'scryfall:bulk' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:bulk' started.");
        Log::channel('scryfall')->info("=======================================================");
        $this->getBulkMetadata();
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:bulk' finished in ".$fd->formatMs($ms).".");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:bulk' finished in ".$fd->formatMs($ms).".");
    }
}
