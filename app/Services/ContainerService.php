<?php

namespace App\Services;

use App\Models\Container;

class ContainerService
{
    /**
     * Serialize a container model to the standard Inertia response shape.
     *
     * The `defaultCard.set` relationship must be loaded on the model before
     * calling this method (eager-load via `->with('defaultCard.set')`).
     *
     * @param  Container  $container
     * @return array{id: string, name: string, description: string|null, type: string, custom_type: string|null, sort: int, defaultCard: array|null}
     */
    public static function serializeContainer(Container $container): array
    {
        return [
            'id'          => $container->id,
            'name'        => $container->name,
            'description' => $container->description,
            'type'        => $container->type,
            'custom_type' => $container->custom_type,
            'sort'        => $container->sort_order,
            'defaultCard' => $container->defaultCard ? [
                'id'      => $container->defaultCard->id,
                'name'    => $container->defaultCard->name,
                'art_crop' => $container->defaultCard->art_crop,
                'set'     => [
                    'name' => $container->defaultCard->set?->name,
                    'code' => $container->defaultCard->set?->code,
                ],
            ] : null,
        ];
    }
}