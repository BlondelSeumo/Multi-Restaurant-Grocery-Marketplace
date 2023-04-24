<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\PaymentProcess
 *
 * @property string $id
 * @property int $user_id
 * @property int $order_id
 * @property int $subscription_id
 * @property array $data
 * @property User|null $user
 * @property Order|null $order
 * @property Subscription|null $subscription
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User filter($filter)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereUserId($value)
 * @method static Builder|User whereOrderId($value)
 * @method static Builder|User whereData($value)
 * @mixin Eloquent
 */
class PaymentProcess extends Model
{
    public $table = 'payment_process';
    public $guarded = [];
    public $timestamps = false;

    public $casts = [
        'id'   => 'string',
        'data' => 'array'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
