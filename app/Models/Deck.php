<?php

namespace App\Models;

use App\Enums\CardFormat;
use App\Enums\ContainerVisibility;
use App\Enums\DeckState;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Deck extends Model
{
    use HasUuids;

    const NAME_MAX = 128;

    const DESCRIPTION_MAX = 2000;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'decks';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'format',
        'visibility',
        'state',
        'bracket',
        'default_card_id',
        'container_id',
    ];

    protected function casts(): array
    {
        return [
            'format' => CardFormat::class,
            'visibility' => ContainerVisibility::class,
            'state' => DeckState::class,
            'bracket' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, Deck>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The default card used as the deck's hero/banner art.
     *
     * @return BelongsTo<DefaultCard, Deck>
     */
    public function defaultCard(): BelongsTo
    {
        return $this->belongsTo(DefaultCard::class, 'default_card_id');
    }

    /**
     * The container (deckbox) this deck is stored in when built.
     *
     * @return BelongsTo<Container, Deck>
     */
    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    /**
     * The commander cards for this deck (Commander/Oathbreaker/Brawl formats).
     *
     * @return BelongsToMany<DefaultCard>
     */
    public function commanders(): BelongsToMany
    {
        return $this->belongsToMany(DefaultCard::class, 'commanders')
            ->using(Commander::class)
            ->withPivot('is_partner');
    }

    /**
     * @return HasMany<DeckCard>
     */
    public function deckCards(): HasMany
    {
        return $this->hasMany(DeckCard::class);
    }

    /**
     * @return HasMany<DeckCategory>
     */
    public function categories(): HasMany
    {
        return $this->hasMany(DeckCategory::class);
    }
}
