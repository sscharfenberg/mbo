<?php

namespace App\Http\Controllers\Collection;

use App\Enums\BinderType;
use App\Http\Controllers\Controller;
use App\Models\Container;
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
        return Inertia::render('Collection/Container/ListContainers', [
            'containerTypes' => array_column(BinderType::cases(), 'value'),
            'containers' => $request->user()->containers()->orderBy('name')->get(),
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
        return Inertia::render('Collection/Container/NewContainer', [
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
            ]);
        });

        $container = Container::create([
            'user_id'     => $request->user()->id,
            'name'        => $request->container_name,
            'description' => $request->container_description,
            'type'        => $request->container_type,
            'custom_type' => $request->container_type === 'other' ? $request->container_type_other : null,
        ]);

        $request->session()->flash('message', __('auth.container_created', ['name' => $container->name]));
        $request->session()->flash('type', 'success');

        return redirect(route('containers'));
    }
}
