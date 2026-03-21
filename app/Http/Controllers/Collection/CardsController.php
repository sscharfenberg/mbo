<?php

namespace App\Http\Controllers\Collection;

use App\Enums\CardCondition;
use App\Enums\CardLanguage;
use App\Enums\FoilType;
use App\Http\Controllers\Controller;
use App\Models\CardStack;
use App\Models\Container;
use App\Models\DefaultCard;
use App\Services\ContainerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
                'id' => $c->id,
                'name' => $c->name,
            ]);

        $backUrl = url()->previous();
        $allowed = [route('collection'), route('containers')];
        if ($container) {
            $allowed[] = route('container.show', $container);
        }
        if (! in_array($backUrl, $allowed)) {
            $backUrl = route('collection');
        }

        return Inertia::render('Collection/AddCards/AddCardsPage', [
            'container' => $container ? ContainerService::serializeContainer($container) : null,
            'containers' => $containers,
            'conditions' => array_column(CardCondition::cases(), 'value'),
            'foilTypes' => array_column(FoilType::cases(), 'value'),
            'languages' => array_column(CardLanguage::cases(), 'value'),
            'backUrl' => $backUrl,
        ]);
    }

    /**
     * Validate and store a new card stack in the user's collection.
     *
     * Wraps validation in a precognitive block so the frontend can perform
     * real-time field validation without triggering the actual store.
     * The container_id is optional — when absent the card is added unsorted.
     */
    public function store(Request $request): RedirectResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'default_card_id' => ['required', Rule::exists(DefaultCard::class, 'id')],
                'amount' => ['required', 'integer', 'min:1', 'max:65535'],
                'language' => ['required', Rule::enum(CardLanguage::class)],
                'container_id' => ['nullable', Rule::exists(Container::class, 'id')],
                'condition' => ['nullable', Rule::enum(CardCondition::class)],
                'foil_type' => ['nullable', Rule::enum(FoilType::class)],
            ]);
        });

        if ($request->container_id) {
            $container = Container::findOrFail($request->container_id);
            abort_if($container->user_id !== $request->user()->id, 403);
        }

        CardStack::create([
            'user_id' => $request->user()->id,
            'default_card_id' => $request->default_card_id,
            'container_id' => $request->container_id ?: null,
            'amount' => $request->amount,
            'condition' => $request->condition ?: null,
            'foil_type' => $request->foil_type ?: null,
            'language' => $request->language,
        ]);

        $request->session()->flash('message', __('collection.card_added'));
        $request->session()->flash('type', 'success');

        if ($request->redirect === 'add_more') {
            return redirect(route('cards.add'));
        }

        $backUrl = $request->input('back_url', route('collection'));
        if (! str_starts_with($backUrl, config('app.url'))) {
            $backUrl = route('collection');
        }

        return redirect($backUrl);
    }
}
