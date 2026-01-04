<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;

use App\Services\Scryfall\PrintedCardsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAllCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:all_cards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update database with all cards from scryfall.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fd = new FormatService();
        $ac = new PrintedCardsService();
        $start = now();
        $this->info("artisan command 'scryfall:all_cards' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:all_cards' started.");
        Log::channel('scryfall')->info("=======================================================");
        $ac->updateAllCards();
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:all_cards' finished in ".$fd->formatMs($ms).".");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:all_cards' finished in ".$fd->formatMs($ms).".");
    }
}
