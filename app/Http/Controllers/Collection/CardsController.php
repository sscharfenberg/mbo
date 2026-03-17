<?php

namespace App\Http\Controllers\Collection;

use App\Enums\CardCondition;
use App\Enums\CardLanguage;
use App\Enums\FoilType;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Services\ContainerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CardsController extends Controller
{
    /**
     * Display the "add cards" page.
     *
     * Serves three use cases:
     * - Add cards to a specific container (when $container is provided via route)
     * - Add cards to the collection unsorted (when no container is specified)
     * - Edit card stack properties (same page, different context)
     *
     * Aborts with 403 if a container is specified but belongs to another user.
     *
     * @param  Request         $request
     * @param  Container|null  $container
     * @return Response
     */
    public function add(Request $request, ?Container $container = null): Response
    {
        if ($container) {
            abort_if($container->user_id !== $request->user()->id, 403);
            $container->load('defaultCard.set', 'defaultCard.artist');
        }

        $containers = $request->user()->containers()
            ->orderBy('sort_order')
            ->get()
            ->map(fn ($c) => [
                'id'   => $c->id,
                'name' => $c->name,
            ]);

        return Inertia::render('Collection/AddCards/AddCardsPage', [
            'container'  => $container ? ContainerService::serializeContainer($container) : null,
            'containers' => $containers,
            'conditions' => array_column(CardCondition::cases(), 'value'),
            'foilTypes'  => array_column(FoilType::cases(), 'value'),
            'languages'  => array_column(CardLanguage::cases(), 'value'),
        ]);
    }
}