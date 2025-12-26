<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
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
    protected $table = 'sets';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'code',
        'name',
        'block_code',
        'block',
        'parent_set_code',
        'card_count',
        'printed_size',
        'set_type',
        'digital',
        'scryfall_uri',
        'icon',
        'released_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'digital' => 'boolean',
        'released_at' => 'date'
    ];

}
