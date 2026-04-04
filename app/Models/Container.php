<?php

namespace App\Models;

use App\Enums\ContainerType;
use App\Enums\ContainerVisibility;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    use HasUuids;

    const NAME_MAX = 128;

    const DESCRIPTION_MAX = 255;

    const CUSTOM_TYPE_MAX = 64;

    const MAX_CONTAINERS = 100;

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
    protected $table = 'containers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'name',
        'description',
        'type',
        'custom_type',
        'default_card_id',
        'visibility',
        'sort_order',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'type' => ContainerType::class,
        'visibility' => ContainerVisibility::class,
    ];

    /**
     * Get the user that owns this container.
     *
     * @return BelongsTo<User, Container>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the default card used as the container's cover image.
     *
     * @return BelongsTo<DefaultCard, Container>
     */
    public function defaultCard(): BelongsTo
    {
        return $this->belongsTo(DefaultCard::class, 'default_card_id');
    }

    /**
     * Get the card stacks stored in this container.
     *
     * @return HasMany<CardStack>
     */
    public function cardStacks(): HasMany
    {
        return $this->hasMany(CardStack::class);
    }
}
