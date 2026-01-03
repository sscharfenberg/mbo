<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;
use App\Services\Scryfall\SetsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateSets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:sets
                            {--full : Make a full update, and re-download every set icon}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all sets from scryfall and update database';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fd = new FormatService();
        $s = new SetsService();
        $start = now();
        $this->info("artisan command 'scryfall:sets' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:sets' started.");
        Log::channel('scryfall')->info("=======================================================");
        $s->updateSets($this->option('full'));
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:sets' finished in ".$fd->formatMs($ms).".");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:sets' finished in ".$fd->formatMs($ms).".");
    }
}
