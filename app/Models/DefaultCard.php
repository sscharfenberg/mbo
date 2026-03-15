<?php

namespace App\Models;

use App\Enums\Scryfall\ScryfallCardLayout;
use App\Enums\Scryfall\ScryfallLang;
use App\Enums\Scryfall\ScryfallRarity;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DefaultCard extends Model
{

    use HasUuids;

    /**
     * The data type of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'default_cards';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Disable use of timestamps. Since we do a full DB insert, we do not need timestamps
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'collector_number',
        'layout',
        'lang',
        'card_image_0',
        'card_image_1',
        'art_crop',
        'finishes',
        'games',
        'prices',
        'digital',
        'rarity',
        'artist_id',
        'set_id',
        'oracle_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'layout'     => ScryfallCardLayout::class,
        'lang'       => ScryfallLang::class,
        'rarity'     => ScryfallRarity::class,
        'art_crop'   => 'string',
        'finishes'   => AsCollection::class,
        'games'      => AsCollection::class,
        'prices'     => AsCollection::class,
        'digital'    => 'boolean',
    ];

    /**
     * Get the oracle card associated with this default card.
     *
     * @return BelongsTo<OracleCard, DefaultCard>
     */
    public function oracle(): BelongsTo
    {
        return $this->belongsTo(OracleCard::class, 'oracle_id', 'oracle_id');
    }

    /**
     * Get the set this default card belongs to.
     *
     * @return BelongsTo<Set, DefaultCard>
     */
    public function set(): BelongsTo
    {
        return $this->belongsTo(Set::class, 'set_id', 'id');
    }

    /**
     * Get the artist associated with this default card.
     *
     * @return BelongsTo<Artist, DefaultCard>
     */
    public function artist(): BelongsTo
    {
        return $this->belongsTo(Artist::class, 'artist_id', 'id');
    }

}
