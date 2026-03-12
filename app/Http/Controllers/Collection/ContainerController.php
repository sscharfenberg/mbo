<?php

namespace App\Http\Controllers\Collection;

use App\Enums\BinderType;
use App\Http\Controllers\Controller;
use App\Models\Container;
use App\Models\DefaultCard;
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
     *
     * @param  Request  $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        $containers = $request->user()->containers()
            ->with('defaultCard')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Collection/Containers/ContainersPage', [
            'containerTypes'        => array_column(BinderType::cases(), 'value'),
            'containersMax'          => Container::MAX_CONTAINERS,
            'containersAmount'      => $containers->count(),
            'canCreateNewContainer' => $containers->count() < Container::MAX_CONTAINERS,
            'containers' => $containers->map(fn ($c) => [
                'id'          => $c->id,
                'name'        => $c->name,
                'description' => $c->description,
                'type'        => $c->type,
                'custom_type' => $c->custom_type,
                'artUrl'      => $c->defaultCard?->art_crops?->first(),
                'sort'        => $c->sort_order,
            ]),
        ]);
    }

    /**
     * Display the "new container" page.
     *
     * Renders the "new container" form
     * with the current request context.
     *
     * @param  Request  $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Collection/Container/ContainerFormPage', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
            'nameMax'        => Container::NAME_MAX,
            'descriptionMax' => Container::DESCRIPTION_MAX,
            'customTypeMax'  => Container::CUSTOM_TYPE_MAX,
        ]);
    }

    /**
     * Validate and store a newly created container.
     *
     * Wraps validation in a precognitive block so the frontend can perform
     * real-time field validation without triggering the actual store.
     * `other_type` is only required when `type` is set to "other".
     *
     * @param  Request  $request
     * @return RedirectResponse
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
            'user_id'         => $request->user()->id,
            'name'            => $request->container_name,
            'description'     => $request->container_description,
            'type'            => $request->container_type,
            'custom_type'     => $request->container_type === 'other' ? $request->container_type_other : null,
            'default_card_id' => $request->container_image ?: null,
            'sort_order'      => $nextSort,
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
     *
     * @param  Request    $request
     * @param  Container  $container
     * @return Response
     */
    public function edit(Request $request, Container $container): Response
    {
        abort_if($container->user_id !== $request->user()->id, 403);

        $container->load('defaultCard.set');

        return Inertia::render('Collection/Container/ContainerFormPage', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
            'nameMax'        => Container::NAME_MAX,
            'descriptionMax' => Container::DESCRIPTION_MAX,
            'customTypeMax'  => Container::CUSTOM_TYPE_MAX,
            'container' => [
                'id'          => $container->id,
                'name'        => $container->name,
                'description' => $container->description,
                'type'        => $container->type,
                'custom_type' => $container->custom_type,
                'defaultCard' => $container->defaultCard ? [
                    'id'      => $container->defaultCard->id,
                    'name'    => $container->defaultCard->name,
                    'art_crop' => $container->defaultCard->art_crops?->first(),
                    'set'     => [
                        'name' => $container->defaultCard->set?->name,
                        'code' => $container->defaultCard->set?->code,
                    ],
                ] : null,
            ],
        ]);
    }

    /**
     * Validate and update an existing container.
     *
     * Validation is identical to store and wrapped in a precognitive block for
     * real-time field feedback. Aborts with 403 if the container belongs to
     * another user.
     *
     * @param  Request    $request
     * @param  Container  $container
     * @return RedirectResponse
     */
    public function update(Request $request, Container $container): RedirectResponse
    {
        abort_if($container->user_id !== $request->user()->id, 403);

        precognitive(function () use ($request) {
            $request->validate([
                'container_name'       => ['required', 'string', 'max:'.Container::NAME_MAX],
                'container_description' => ['max:'.Container::DESCRIPTION_MAX],
                'container_type'       => ['required', 'string', Rule::enum(BinderType::class)],
                'container_type_other' => ['required_if:container_type,other', 'string', 'max:'.Container::CUSTOM_TYPE_MAX],
                'container_image'      => ['nullable', Rule::exists(DefaultCard::class, 'id')],
            ]);
        });

        $container->update([
            'name'            => $request->container_name,
            'description'     => $request->container_description,
            'type'            => $request->container_type,
            'custom_type'     => $request->container_type === 'other' ? $request->container_type_other : null,
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
     *
     * @param  Request  $request
     * @return JsonResponse
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'order'   => ['required', 'array'],
            'order.*' => ['uuid'],
        ]);

        $userContainerIds = $request->user()->containers()->pluck('id')->flip();

        foreach ($request->order as $index => $id) {
            if (!$userContainerIds->has($id)) {
                continue;
            }
            Container::where('id', $id)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['ok' => true]);
    }
}
