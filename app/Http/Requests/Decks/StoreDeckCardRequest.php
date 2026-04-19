<?php

namespace App\Http\Requests\Decks;

use App\Enums\DeckZone;
use App\Models\Deck;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDeckCardRequest extends FormRequest
{
    /**
     * The authenticated user must own the deck.
     */
    public function authorize(): bool
    {
        $deck = $this->route('deck');

        return $deck instanceof Deck && $deck->user_id === $this->user()->id;
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function rules(): array
    {
        return [
            'default_card_id' => ['required', 'uuid', 'exists:default_cards,id'],
            'zone' => ['required', Rule::enum(DeckZone::class)],
        ];
    }
}
