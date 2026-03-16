<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\BulkData;
use App\Models\DefaultCard;
use App\Models\OracleCard;
use App\Models\Set;
use App\Models\Symbol;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{

    /**
     * Get the number and total size of cached art crop images.
     *
     * @return array{num: int, size: int}
     */
    private function getArtCropStats(): array
    {
        $path = storage_path('app/art-crops');
        if (!is_dir($path)) {
            return ['num' => 0, 'size' => 0];
        }
        return [
            'num' => (int) trim(shell_exec("find -L $path -type f | wc -l")),
            'size' => (int) trim(shell_exec("du -sLb $path | cut -f1")),
        ];
    }

    /**
     * Get the number and total size of card images.
     *
     * @return array{num: int, size: int}
     */
    private function getCardImageStats(): array
    {
        $path = storage_path('app/card-images');
        if (!is_dir($path)) {
            return ['num' => 0, 'size' => 0];
        }
        return [
            'num' => (int) trim(shell_exec("find -L $path -type f | wc -l")),
            'size' => (int) trim(shell_exec("du -sLb $path | cut -f1")),
        ];
    }

    /**
     * Display the welcome / landing page.
     *
     * Public entry point of the application, shown to unauthenticated visitors.
     *
     * @param  Request  $request
     * @return \Inertia\Response
     */
    public function show(Request $request): \Inertia\Response
    {
        return Inertia::render('Guest/Welcome/Welcome', [
            'oracleCards' => [
                'num' => OracleCard::count(),
                'size' => BulkData::where('type', 'oracle_cards')->first()?->size,
            ],
            'defaultCards' => [
                'num' => DefaultCard::count(),
                'size' => BulkData::where('type', 'default_cards')->first()?->size,
            ],
            'sets' => Set::count(),
            'artists' => Artist::count(),
            'symbols' => Symbol::where('funny', false)->inRandomOrder()->limit(10)->get(['path', 'english']),
            'artCrops' => $this->getArtCropStats(),
            'cardImages' => $this->getCardImageStats()
        ]);
    }

}
