<?php

namespace App\Services;

use App\Models\CardStack;
use App\Models\User;

class CardStackService
{
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
}
