<?php

namespace App\Http\Controllers\Collection;

use App\Enums\BinderType;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\DefaultCard;
use App\Services\CardSearchParser;
use App\Services\ContainerService;
use App\Services\DataTableService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ContainerController extends Controller
{
    /**
     * Display the containers list.
     *
     * Renders the "Listcontainers" page
     * with the current request context.
     */
    public function list(Request $request): Response
    {
        $containers = $request->user()->containers()
            ->with('defaultCard.set', 'defaultCard.artist')
            ->withSum('cardStacks', 'amount')
            ->addSelect([
                'total_price' => ContainerService::totalPriceSubquery($request->user()->currency),
            ])
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Collection/Containers/ContainersPage', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
            'containersMax' => Container::MAX_CONTAINERS,
            'containersAmount' => $containers->count(),
            'canCreateNewContainer' => $containers->count() < Container::MAX_CONTAINERS,
            'containers' => $containers->map(fn ($c) => ContainerService::serializeContainer($c)),
        ]);
    }

    /**
     * Display the "new container" page.
     *
     * Renders the "new container" form
     * with the current request context.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Collection/Container/ContainerFormPage', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
            'nameMax' => Container::NAME_MAX,
            'descriptionMax' => Container::DESCRIPTION_MAX,
            'customTypeMax' => Container::CUSTOM_TYPE_MAX,
        ]);
    }

    /**
     * Validate and store a newly created container.
     *
     * Wraps validation in a precognitive block so the frontend can perform
     * real-time field validation without triggering the actual store.
     * `other_type` is only required when `type` is set to "other".
     */
    public function store(Request $request): RedirectResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'container_name' => ['required', 'string', 'max:'.Container::NAME_MAX],
                'container_description' => ['max:'.Container::DESCRIPTION_MAX],
                'container_type' => ['required', 'string', Rule::enum(BinderType::class)],
                'container_type_other' => ['required_if:container_type,other', 'string', 'max:'.Container::CUSTOM_TYPE_MAX],
                'container_image' => ['nullable', Rule::exists(DefaultCard::class, 'id')],
            ]);
        });

        $container = ContainerService::createContainer($request->user(), $request->only([
            'container_name', 'container_description', 'container_type', 'container_type_other', 'container_image',
        ]));

        $request->session()->flash('message', __('auth.container_created', ['name' => $container->name]));
        $request->session()->flash('type', 'success');

        return redirect(route('containers'));
    }

    /**
     * Display the "edit container" page.
     *
     * Loads the container with its default card and set so the form can be
     * pre-populated. Aborts with 403 if the container belongs to another user.
     */
    public function edit(Request $request, Container $container): Response
    {
        abort_if($container->user_id !== $request->user()->id, 403);

        $container->load('defaultCard.set', 'defaultCard.artist');

        return Inertia::render('Collection/Container/ContainerFormPage', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
            'nameMax' => Container::NAME_MAX,
            'descriptionMax' => Container::DESCRIPTION_MAX,
            'customTypeMax' => Container::CUSTOM_TYPE_MAX,
            'container' => ContainerService::serializeContainer($container),
        ]);
    }

    /**
     * Validate and update an existing container.
     *
     * Validation is identical to store and wrapped in a precognitive block for
     * real-time field feedback. Aborts with 403 if the container belongs to
     * another user.
     */
    public function update(Request $request, Container $container): RedirectResponse
    {
        precognitive(function () use ($request) {
            $request->validate([
                'container_name' => ['required', 'string', 'max:'.Container::NAME_MAX],
                'container_description' => ['max:'.Container::DESCRIPTION_MAX],
                'container_type' => ['required', 'string', Rule::enum(BinderType::class)],
                'container_type_other' => ['required_if:container_type,other', 'string', 'max:'.Container::CUSTOM_TYPE_MAX],
                'container_image' => ['nullable', Rule::exists(DefaultCard::class, 'id')],
            ]);
        });

        ContainerService::updateContainer($request->user(), $container, $request->only([
            'container_name', 'container_description', 'container_type', 'container_type_other', 'container_image',
        ]));

        $request->session()->flash('message', __('auth.container_updated', ['name' => $container->name]));
        $request->session()->flash('type', 'success');

        return redirect(route('containers'));
    }

    /**
     * Persist a new sort order for the user's containers.
     *
     * Expects `order`: an array of container UUIDs in the desired order.
     * Only IDs that belong to the authenticated user are accepted —
     * any unknown or foreign IDs are silently ignored.
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['uuid'],
        ]);

        ContainerService::reorder($request->user(), $request->order);

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a container.
     *
     * Redirects back to the containers list with a success flash message.
     */
    public function destroy(Request $request, Container $container): RedirectResponse
    {
        $name = ContainerService::deleteContainer($request->user(), $container);

        $request->session()->flash('message', __('auth.container_deleted', ['name' => $name]));
        $request->session()->flash('type', 'success');

        return redirect(route('containers'));
    }

    /**
     * Display a single container's detail page.
     *
     * Aborts with 403 if the container belongs to another user.
     * Route model binding automatically returns 404 if the container does not exist.
     */
    public function show(Request $request, Container $container): Response
    {
        abort_if($container->user_id !== $request->user()->id, 403);

        $currency = $request->user()->currency;

        $container->load('defaultCard.set', 'defaultCard.artist');
        $container->loadSum('cardStacks', 'amount');

        $container->total_price = ContainerService::totalPrice($container, $currency);

        $unitPriceSql = ContainerService::unitPriceSql($currency);

        $query = $container->cardStacks()
            ->join('default_cards', 'card_stacks.default_card_id', '=', 'default_cards.id')
            ->leftJoin('sets', 'default_cards.set_id', '=', 'sets.id')
            ->select([
                'card_stacks.*',
                'default_cards.name as card_name',
                'default_cards.collector_number',
                'default_cards.art_crop',
                'sets.name as set_name',
                'sets.code as set_code',
                'sets.path as set_icon',
            ])
            ->selectRaw("COALESCE({$unitPriceSql}, 0) as unit_price")
            ->selectRaw("COALESCE(card_stacks.amount * ({$unitPriceSql}), 0) as stack_price");

        $table = DataTableService::buildResponse(
            query: $query,
            request: $request,
            sortable: ['name', 'set_name', 'collector_number', 'amount', 'condition', 'language', 'finish', 'price', 'total_price'],
            sortColumnMap: [
                'name' => 'default_cards.name',
                'set_name' => 'sets.name',
                'collector_number' => 'default_cards.collector_number',
                'price' => 'unit_price',
                'total_price' => 'stack_price',
            ],
            defaultSort: 'name',
            searchCallback: function ($q, $search) {
                $parsed = CardSearchParser::parse($search);

                if (! $parsed) {
                    return;
                }

                if ($parsed['set_code']) {
                    $q->where('sets.code', $parsed['set_code']);
                }

                if ($parsed['collector_number']) {
                    $q->where('default_cards.collector_number', $parsed['collector_number']);
                }

                foreach ($parsed['name_segments'] as $segment) {
                    $q->where(function ($q) use ($segment) {
                        $q->where('default_cards.name', 'like', "%{$segment}%")
                            ->orWhere('sets.name', 'like', "%{$segment}%");
                    });
                }
            },
            rowMapper: function ($stack) {
                return [
                    'id' => $stack->id,
                    'name' => $stack->card_name,
                    'set_name' => $stack->set_name,
                    'set_code' => $stack->set_code,
                    'set_icon' => $stack->set_icon,
                    'collector_number' => $stack->collector_number,
                    'amount' => $stack->amount,
                    'condition' => $stack->condition?->value,
                    'finish' => $stack->finish?->label(),
                    'language' => $stack->language?->value ?? 'en',
                    'art_crop' => $stack->art_crop,
                    'price' => (float) ($stack->unit_price ?? 0),
                    'total_price' => (float) ($stack->stack_price ?? 0),
                ];
            },
        );

        $containers = Container::query()
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $container->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Collection/Container/ContainerPage', [
            'container' => ContainerService::serializeContainer($container),
            'table' => $table,
            'containers' => $containers,
        ]);
    }
}
