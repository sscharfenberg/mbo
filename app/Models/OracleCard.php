<?php

namespace App\Models;

use App\Enums\Scryfall\ScryfallCardLayout;
use App\Enums\Scryfall\ScryfallLang;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OracleCard extends Model
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
    protected $table = 'oracle_cards';

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
        'cmc',
        'color_identity',
        'reserved',
        'game_changer',
        'scryfall_uri',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'reserved' => 'boolean',
        'game_changer' => 'boolean',
        'layout' => ScryfallCardLayout::class,
        'lang' => ScryfallLang::class,
        'cmc' => 'float',
    ];

    /**
     * Get all legality entries for this oracle card.
     *
     * Formats where the card is not legal have no row — absence means not legal.
     */
    public function legalities(): HasMany
    {
        return $this->hasMany(OracleCardLegality::class, 'oracle_card_id');
    }

    /**
     * Get all default versions of this oracle card.
     */
    public function defaults(): HasMany
    {
        return $this->hasMany(DefaultCard::class, 'oracle_id', 'oracle_id');
    }

    /**
     * Get the card faces (1 for single-faced cards, 2 for multi-faced).
     *
     * @return HasMany<OracleCardFace>
     */
    public function faces(): HasMany
    {
        return $this->hasMany(OracleCardFace::class);
    }
}
