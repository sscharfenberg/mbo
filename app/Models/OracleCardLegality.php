<?php

namespace App\Models;

use App\Enums\CardLegality;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OracleCardLegality extends Model
{
    protected $table = 'legalities';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'oracle_card_id',
        'format',
        'legality',
    ];

    protected function casts(): array
    {
        return [
            'legality' => CardLegality::class,
        ];
    }

    public function oracleCard(): BelongsTo
    {
        return $this->belongsTo(OracleCard::class, 'oracle_card_id');
    }
}
