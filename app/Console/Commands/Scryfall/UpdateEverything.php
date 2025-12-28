<?php

namespace App\Console\Commands\Scryfall;

use App\Services\FormatService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateEverything extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scryfall:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update every scryfall resource.';

    /**
     * The day of the week where a full set update is done (including re-downloading all icons)
     *
     * @var int
     */
    protected int $fullSetUpdateWeekDay = 7;

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $start = now();
        $fd = new FormatService();
        $waitTime = 0;
        $this->info("artisan command 'scryfall:update' started.");
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:update' started.");
        Log::channel('scryfall')->info("=======================================================");
        // update sets
        if (Carbon::now()->dayOfWeek === $this->fullSetUpdateWeekDay) {
            $this->call('scryfall:sets --full');
        } else {
            $this->call('scryfall:sets');
        }
        sleep(2);
        $waitTime += 2;
        // bulk data. we need this information for all of the other commands
        $this->call('scryfall:bulk');
        sleep(2);
        $waitTime += 2;
        // update oracle cards
        $this->call('scryfall:oracle');
        sleep(2);
        $waitTime += 2;
        $ms = $start->diffInMilliseconds(now());
        Log::channel('scryfall')->info("=======================================================");
        Log::channel('scryfall')->info("artisan command 'scryfall:update' finished in ".$fd->formatMs($ms).", including $waitTime seconds idle time.");
        Log::channel('scryfall')->info("=======================================================");
        $this->info("artisan command 'scryfall:update' finished in ".$fd->formatMs($ms).", including $waitTime seconds idle time.");
    }
}
