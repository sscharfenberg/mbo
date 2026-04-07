<?php

namespace App\Models;

use App\Enums\CardLanguage;
use App\Enums\DeckZone;
use App\Enums\Finish;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeckCard extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'deck_cards';

    protected $primaryKey = 'id';

    protected $fillable = [
        'deck_id',
        'oracle_card_id',
        'default_card_id',
        'card_stack_id',
        'category_id',
        'zone',
        'quantity',
        'finish',
        'language',
    ];

    protected function casts(): array
    {
        return [
            'zone' => DeckZone::class,
            'finish' => Finish::class,
            'language' => CardLanguage::class,
            'quantity' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Deck, DeckCard>
     */
    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * The oracle card (logical identity) this deck card references.
     *
     * @return BelongsTo<OracleCard, DeckCard>
     */
    public function oracleCard(): BelongsTo
    {
        return $this->belongsTo(OracleCard::class);
    }

    /**
     * The specific printing this deck card references.
     *
     * @return BelongsTo<DefaultCard, DeckCard>
     */
    public function defaultCard(): BelongsTo
    {
        return $this->belongsTo(DefaultCard::class);
    }

    /**
     * The physical card from the collection assigned to this deck slot.
     *
     * @return BelongsTo<CardStack, DeckCard>
     */
    public function cardStack(): BelongsTo
    {
        return $this->belongsTo(CardStack::class);
    }

    /**
     * The user-defined category this card is assigned to, if any.
     *
     * @return BelongsTo<DeckCategory, DeckCard>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(DeckCategory::class);
    }
}
