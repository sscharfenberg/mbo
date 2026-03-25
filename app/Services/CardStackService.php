<?php

namespace App\Services;

use App\Models\CardStack;
use App\Models\Container;
use App\Models\User;
use Illuminate\Support\Collection;

class CardStackService
{
    /**
     * Verify that a container belongs to the given user.
     *
     * Aborts with 403 if ownership does not match.
     * Returns the loaded container, or null when no container_id is given.
     */
    public static function resolveOwnedContainer(User $user, ?string $containerId): ?Container
    {
        if (! $containerId) {
            return null;
        }

        $container = Container::findOrFail($containerId);
        abort_if($container->user_id !== $user->id, 403);

        return $container;
    }

    /**
     * Add cards to the user's collection, merging into an existing stack when
     * one already exists with the same identifying attributes.
     *
     * A stack is uniquely identified by user_id + default_card_id + language +
     * condition + foil_type + container_id (including null matches).
     *
     * @param  array{default_card_id: string, amount: int, language: string, container_id?: string|null, condition?: string|null, foil_type?: string|null}  $data
     * @return array{stack: CardStack, merged: bool}
     */
    public static function addToCollection(User $user, array $data): array
    {
        $attributes = [
            'user_id' => $user->id,
            'default_card_id' => $data['default_card_id'],
            'language' => $data['language'],
            'condition' => $data['condition'] ?: null,
            'foil_type' => $data['foil_type'] ?: null,
            'container_id' => $data['container_id'] ?: null,
        ];

        $existing = CardStack::where($attributes)->first();

        if ($existing) {
            $existing->increment('amount', (int) $data['amount']);

            return ['stack' => $existing, 'merged' => true];
        }

        return [
            'stack' => CardStack::create([
                ...$attributes,
                'amount' => $data['amount'],
            ]),
            'merged' => false,
        ];
    }

    /**
     * Update a card stack's editable attributes.
     *
     * Aborts with 403 if the stack does not belong to the user.
     *
     * @param  array{amount: int, language: string, condition?: string|null, foil_type?: string|null, container_id?: string|null}  $data
     */
    public static function updateStack(User $user, CardStack $cardStack, array $data): void
    {
        abort_if($cardStack->user_id !== $user->id, 403);

        $cardStack->update([
            'amount' => $data['amount'],
            'language' => $data['language'],
            'condition' => $data['condition'] ?: null,
            'foil_type' => $data['foil_type'] ?: null,
            'container_id' => $data['container_id'] ?: null,
        ]);
    }

    /**
     * Delete a card stack from the user's collection.
     *
     * Aborts with 403 if the stack does not belong to the user.
     *
     * @return array{name: string, amount: int, container_id: string|null} Metadata for the flash message.
     */
    public static function deleteStack(User $user, CardStack $cardStack): array
    {
        abort_if($cardStack->user_id !== $user->id, 403);

        $meta = [
            'name' => $cardStack->defaultCard->name,
            'amount' => $cardStack->amount,
            'container_id' => $cardStack->container_id,
        ];

        $cardStack->delete();

        return $meta;
    }

    /**
     * Verify ownership and move card stacks to a different container.
     *
     * Aborts with 403 if any stack does not belong to the user,
     * or 404 if any requested ID does not exist.
     *
     * @param  string[]  $cardStackIds
     * @return Collection<int, CardStack> The affected stacks.
     */
    public static function moveToContainer(User $user, array $cardStackIds, ?string $containerId): Collection
    {
        $stacks = CardStack::whereIn('id', $cardStackIds)->get();
        abort_if($stacks->contains(fn (CardStack $s) => $s->user_id !== $user->id), 403);
        abort_if($stacks->count() !== count($cardStackIds), 404);

        CardStack::whereIn('id', $cardStackIds)
            ->update(['container_id' => $containerId]);

        return $stacks;
    }
}
