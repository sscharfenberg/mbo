<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrintedCard extends Model
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
    protected $table = 'printed_cards';

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
        'image_uris',
        'finishes',
        'games',
        'prices',
        'digital',
        'rarity',
        'set_id',
        'oracle_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'image_uris' => AsCollection::class,
        'finishes' => AsCollection::class,
        'games' => AsCollection::class,
        'prices' => AsCollection::class,
        'digital' => 'boolean'
    ];

    /**
     * Get the oracle card associated with this printed card.
     * @return belongsTo
     */
    public function oracle(): belongsTo
    {
        return $this->belongsTo(OracleCard::class, 'oracle_id', 'oracle_id');
    }

    /**
     * Get the artist associated with the song.
     * @return belongsTo
     */
    public function set(): belongsTo
    {
        return $this->belongsTo(Set::class, 'set_id', 'id');
    }

}
