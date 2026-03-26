<?php

namespace App\Models;

use App\Enums\CardCondition;
use App\Enums\CardLanguage;
use App\Enums\Finish;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CardStack extends Model
{
    use HasUuids;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'card_stacks';

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
        'user_id',
        'default_card_id',
        'container_id',
        'amount',
        'condition',
        'finish',
        'language',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'condition' => CardCondition::class,
        'finish' => Finish::class,
        'language' => CardLanguage::class,
        'amount' => 'integer',
    ];

    /**
     * The user who owns this card stack.
     *
     * @return BelongsTo<User, CardStack>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The specific printing (default card) this stack represents.
     *
     * @return BelongsTo<DefaultCard, CardStack>
     */
    public function defaultCard(): BelongsTo
    {
        return $this->belongsTo(DefaultCard::class);
    }

    /**
     * The container this stack is stored in, if any.
     *
     * @return BelongsTo<Container, CardStack>
     */
    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }
}
