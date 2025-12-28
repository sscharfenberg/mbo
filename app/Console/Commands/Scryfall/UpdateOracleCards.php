<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;
use App\Services\Scryfall\ScryfallOracleCardsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateOracleCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:oracle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update database with oracle cards from scryfall.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fd = new FormatService();
        $socs = new ScryfallOracleCardsService();
        $start = now();
        $this->info("artisan command 'scryfall:oracle' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:oracle' started.");
        Log::channel('scryfall')->info("=======================================================");
        $socs->updateOracleCards();
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:oracle' finished in ".$fd->formatMs($ms).".");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:oracle' finished in ".$fd->formatMs($ms).".");
    }
}
