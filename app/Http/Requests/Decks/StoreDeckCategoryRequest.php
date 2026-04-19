<?php

namespace App\Http\Requests\Decks;

use App\Models\Deck;
use App\Models\DeckCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreDeckCategoryRequest extends FormRequest
{
    /**
     * The user may create a category only on their own deck.
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
            'group_name' => ['required', 'string', 'max:'.DeckCategory::NAME_MAX],
            'card_id' => ['nullable', 'uuid', 'exists:deck_cards,id'],
        ];
    }
}
