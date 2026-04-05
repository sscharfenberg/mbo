<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OracleCardFace extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $table = 'oracle_card_faces';

    protected $primaryKey = 'id';

    /**
     * Disable timestamps. Populated via bulk Scryfall import.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $fillable = [
        'id',
        'oracle_card_id',
        'face_index',
        'name',
        'mana_cost',
        'type_line',
        'oracle_text',
        'colors',
        'power',
        'toughness',
        'loyalty',
        'defense',
    ];

    protected $casts = [
        'face_index' => 'integer',
    ];

    /**
     * @return BelongsTo<OracleCard, OracleCardFace>
     */
    public function oracleCard(): BelongsTo
    {
        return $this->belongsTo(OracleCard::class);
    }
}
