<?php

namespace App\Models;

use App\Traits\Loadable;
use Database\Factories\TransactionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Payout
 *
 * @property int $id
 * @property string $status
 * @property string $cause
 * @property string $answer
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Order|null $order
 * @property Collection|Gallery[] $galleries
 * @method static TransactionFactory factory(...$parameters)
 * @method static Builder|OrderRefund filter($array = [])
 * @method static Builder|OrderRefund newModelQuery()
 * @method static Builder|OrderRefund newQuery()
 * @method static Builder|OrderRefund query()
 * @method static Builder|OrderRefund whereCreatedAt($value)
 * @method static Builder|OrderRefund whereDeletedAt($value)
 * @method static Builder|OrderRefund whereUpdatedAt($value)
 * @method static Builder|OrderRefund whereId($value)
 * @method static Builder|OrderRefund whereCause($value)
 * @method static Builder|OrderRefund whereAnswer($value)
 * @method static Builder|OrderRefund whereStatus($value)
 * @mixin Eloquent
 */
class OrderRefund extends Model
{
    use HasFactory, SoftDeletes, Loadable;

    protected $guarded = ['id'];

    const STATUS_PENDING    = 'pending';
    const STATUS_ACCEPTED   = 'accepted';
    const STATUS_CANCELED   = 'canceled';

    const STATUSES = [
        self::STATUS_PENDING    => self::STATUS_PENDING,
        self::STATUS_ACCEPTED   => self::STATUS_ACCEPTED,
        self::STATUS_CANCELED   => self::STATUS_CANCELED,
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function scopeFilter($query, $filter = [])
    {
        return $query
            ->when(data_get($filter, 'order_id'), function ($q, $orderId) {
                $q->where('order_id', $orderId);
            })
            ->when(data_get($filter, 'shop_id') || data_get($filter, 'user_id'), function ($q) use ($filter) {
                $q->whereHas('order', function ($builder) use ($filter) {
                    if (data_get($filter, 'shop_id')) {
                        $builder->where('shop_id', data_get($filter, 'shop_id'));
                    }
                    if (data_get($filter, 'user_id')) {
                        $builder->where('user_id', data_get($filter, 'user_id'));
                    }
                });
            })
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->when(data_get($filter, 'status'), function ($q, $status) {
                $q->where('status', $status);
            })
            ->when(data_get($filter, 'cause'), function ($q, $cause) {
                $q->where('cause', 'like', "%$cause%");
            })
            ->when(data_get($filter, 'answer'), function ($q, $answer) {
                $q->where('answer', 'like', "%$answer%");
            })
            ->when(data_get($filter, 'search'), function (Builder $query, $search) {
                $query->where(function (Builder $q) use ($search) {
                    $q->where('answer', 'like', "%$search%")
                        ->orWhere('cause', 'like', "%$search%")
                        ->orWhere('id', 'like', "%$search%")
                        ->orWhere('order_id', 'like', "%$search%")
                        ->orWhereHas('order.shop.translation', fn($q) => $q->where('title', 'like', "%$search%"))
                        ->orWhereHas('order.user',
                            fn($q) => $q->where('firstname', 'like', "%$search%")->orWhere('lastname', 'like', "%$search")
                        );
                });
            });
    }
}
