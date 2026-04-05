<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeckCategory extends Model
{
    use HasUuids;

    const NAME_MAX = 64;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'deck_categories';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'deck_id',
        'name',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    /**
     * @return BelongsTo<Deck, DeckCategory>
     */
    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * @return HasMany<DeckCard>
     */
    public function deckCards(): HasMany
    {
        return $this->hasMany(DeckCard::class, 'category_id');
    }
}
