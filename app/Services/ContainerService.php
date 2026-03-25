<?php

namespace App\Services;

use App\Models\Container;
use App\Models\User;

class ContainerService
{
    /**
     * Serialize a container model to the standard Inertia response shape.
     *
     * The `defaultCard.set` and `defaultCard.artist` relationships must be
     * loaded on the model before calling this method
     * (eager-load via `->with('defaultCard.set', 'defaultCard.artist')`).
     *
     * When the `card_stacks_sum_amount` aggregate is loaded on the model
     * (via `withSum('cardStacks', 'amount')`), it is included as `totalCards`.
     *
     * @return array{id: string, name: string, description: string|null, type: string, custom_type: string|null, sort: int, defaultCard: array|null, totalCards: int}
     */
    public static function serializeContainer(Container $container): array
    {
        return [
            'id' => $container->id,
            'name' => $container->name,
            'description' => $container->description,
            'type' => $container->type,
            'custom_type' => $container->custom_type,
            'sort' => $container->sort_order,
            'defaultCard' => $container->defaultCard ? [
                'id' => $container->defaultCard->id,
                'name' => $container->defaultCard->name,
                'art_crop' => $container->defaultCard->art_crop,
                'artist' => $container->defaultCard->artist?->name,
                'set' => [
                    'name' => $container->defaultCard->set?->name,
                    'code' => $container->defaultCard->set?->code,
                ],
            ] : null,
            'totalCards' => (int) ($container->card_stacks_sum_amount ?? 0),
        ];
    }

    /**
     * Create a new container for the user.
     *
     * Aborts with 422 if the user has reached the container limit.
     * Automatically assigns the next sort_order.
     *
     * @param  array{container_name: string, container_description?: string|null, container_type: string, container_type_other?: string|null, container_image?: string|null}  $data
     */
    public static function createContainer(User $user, array $data): Container
    {
        abort_if($user->containers()->count() >= Container::MAX_CONTAINERS, 422);

        $nextSort = ($user->containers()->max('sort_order') ?? 0) + 1;

        return Container::create([
            'user_id' => $user->id,
            'name' => $data['container_name'],
            'description' => $data['container_description'] ?? null,
            'type' => $data['container_type'],
            'custom_type' => $data['container_type'] === 'other' ? ($data['container_type_other'] ?? null) : null,
            'default_card_id' => $data['container_image'] ?: null,
            'sort_order' => $nextSort,
        ]);
    }

    /**
     * Update an existing container's attributes.
     *
     * Aborts with 403 if the container does not belong to the user.
     *
     * @param  array{container_name: string, container_description?: string|null, container_type: string, container_type_other?: string|null, container_image?: string|null}  $data
     */
    public static function updateContainer(User $user, Container $container, array $data): void
    {
        abort_if($container->user_id !== $user->id, 403);

        $container->update([
            'name' => $data['container_name'],
            'description' => $data['container_description'] ?? null,
            'type' => $data['container_type'],
            'custom_type' => $data['container_type'] === 'other' ? ($data['container_type_other'] ?? null) : null,
            'default_card_id' => $data['container_image'] ?: null,
        ]);
    }

    /**
     * Persist a new sort order for the user's containers.
     *
     * Only IDs that belong to the user are updated — unknown or
     * foreign IDs are silently ignored.
     *
     * @param  string[]  $order  Container UUIDs in desired order.
     */
    public static function reorder(User $user, array $order): void
    {
        $userContainerIds = $user->containers()->pluck('id')->flip();

        foreach ($order as $index => $id) {
            if (! $userContainerIds->has($id)) {
                continue;
            }
            Container::where('id', $id)->update(['sort_order' => $index + 1]);
        }
    }

    /**
     * Delete a container.
     *
     * Aborts with 403 if the container does not belong to the user.
     *
     * @return string The container name (for the flash message).
     */
    public static function deleteContainer(User $user, Container $container): string
    {
        abort_if($container->user_id !== $user->id, 403);

        $name = $container->name;
        $container->delete();

        return $name;
    }
}
