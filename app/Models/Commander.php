<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Commander extends Pivot
{
    protected $table = 'commanders';

    public $incrementing = false;

    public $timestamps = false;

    protected $casts = [
        'is_partner' => 'boolean',
    ];

    /**
     * @return BelongsTo<Deck, Commander>
     */
    public function deck(): BelongsTo
    {
        return $this->belongsTo(Deck::class);
    }

    /**
     * @return BelongsTo<OracleCard, Commander>
     */
    public function oracleCard(): BelongsTo
    {
        return $this->belongsTo(OracleCard::class);
    }

    /**
     * @return BelongsTo<DefaultCard, Commander>
     */
    public function defaultCard(): BelongsTo
    {
        return $this->belongsTo(DefaultCard::class);
    }
}
