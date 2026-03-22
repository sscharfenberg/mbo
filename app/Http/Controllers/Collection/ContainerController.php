<?php

namespace App\Http\Controllers\Collection;

use App\Enums\BinderType;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\DefaultCard;
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

        if ($request->user()->containers()->count() >= Container::MAX_CONTAINERS) {
            abort(422);
        }

        $nextSort = ($request->user()->containers()->max('sort_order') ?? 0) + 1;

        $container = Container::create([
            'user_id' => $request->user()->id,
            'name' => $request->container_name,
            'description' => $request->container_description,
            'type' => $request->container_type,
            'custom_type' => $request->container_type === 'other' ? $request->container_type_other : null,
            'default_card_id' => $request->container_image ?: null,
            'sort_order' => $nextSort,
        ]);

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
        abort_if($container->user_id !== $request->user()->id, 403);

        precognitive(function () use ($request) {
            $request->validate([
                'container_name' => ['required', 'string', 'max:'.Container::NAME_MAX],
                'container_description' => ['max:'.Container::DESCRIPTION_MAX],
                'container_type' => ['required', 'string', Rule::enum(BinderType::class)],
                'container_type_other' => ['required_if:container_type,other', 'string', 'max:'.Container::CUSTOM_TYPE_MAX],
                'container_image' => ['nullable', Rule::exists(DefaultCard::class, 'id')],
            ]);
        });

        $container->update([
            'name' => $request->container_name,
            'description' => $request->container_description,
            'type' => $request->container_type,
            'custom_type' => $request->container_type === 'other' ? $request->container_type_other : null,
            'default_card_id' => $request->container_image ?: null,
        ]);

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

        $userContainerIds = $request->user()->containers()->pluck('id')->flip();

        foreach ($request->order as $index => $id) {
            if (! $userContainerIds->has($id)) {
                continue;
            }
            Container::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a container.
     *
     * Aborts with 403 if the container belongs to another user.
     * Redirects back to the containers list with a success flash message.
     */
    public function destroy(Request $request, Container $container): RedirectResponse
    {
        abort_if($container->user_id !== $request->user()->id, 403);

        $name = $container->name;
        $container->delete();

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

        $container->load('defaultCard.set', 'defaultCard.artist');

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
            ]);

        $table = DataTableService::buildResponse(
            query: $query,
            request: $request,
            sortable: ['name', 'set_name', 'collector_number', 'amount', 'condition', 'language', 'foil_type'],
            sortColumnMap: [
                'name' => 'default_cards.name',
                'set_name' => 'sets.name',
                'collector_number' => 'default_cards.collector_number',
            ],
            defaultSort: 'name',
            searchCallback: function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('default_cards.name', 'like', "%{$search}%")
                        ->orWhere('sets.name', 'like', "%{$search}%")
                        ->orWhere('sets.code', 'like', "%{$search}%");
                });
            },
            rowMapper: function ($stack) {
                return [
                    'id' => $stack->id,
                    'name' => $stack->card_name,
                    'set_name' => $stack->set_name,
                    'set_code' => $stack->set_code,
                    'collector_number' => $stack->collector_number,
                    'amount' => $stack->amount,
                    'condition' => $stack->condition?->value,
                    'foil_type' => $stack->foil_type?->value,
                    'language' => $stack->language?->value ?? 'en',
                    'art_crop' => $stack->art_crop,
                ];
            },
        );

        return Inertia::render('Collection/Container/ContainerPage', [
            'container' => ContainerService::serializeContainer($container),
            'table' => $table,
        ]);
    }
}
